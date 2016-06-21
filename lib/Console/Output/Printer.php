<?php namespace Matura\Console\Output;

use Matura\Core\Result;
use Matura\Events\Event;

use Matura\Blocks\Block;
use Matura\Blocks\Suite;
use Matura\Blocks\Describe;
use Matura\Exceptions\Exception as MaturaException;

function indent_width(Block $block, $per_level = 1)
{
    $level = $block->depth() - 1;
    return ($level * $per_level);
}

function indent($lvl, $string, $per_level = 1)
{
    if (empty($string)) {
        return '';
    } else {
        $indent = str_repeat(" ", $lvl*1);
        return $indent.implode(explode("\n", $string), "\n".$indent);
    }
}

function tag($tag)
{
    $rest = array_slice(func_get_args(), 1);
    $text = implode($rest);
    return "<$tag>$text</$tag>";
}

function pad_left($length, $string, $char = ' ')
{
    return str_pad($string, $length, $char, STR_PAD_LEFT);
}

function pad_right($length, $string, $char = ' ')
{
    return str_pad($string, $length, $char, STR_PAD_RIGHT);
}

/**
 * Contains test rendering methods.
 */
class Printer
{
    protected $options = [
        'trace_depth' => 7,
        'indent' => 3
    ];

    protected $test_count = 0;

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function onTestComplete(Event $event)
    {
        $index        = $this->test_count;

        // Via TestMethod
        $indent_width = ($event->test->depth() - 1) * 2;
        $name         = $event->test->getName();

        // Via Result
        $style        = $event->result->getStatusString();
        $status       = $event->result->getStatus();

        $icon_map = [
            Result::SUCCESS => '✓',
            Result::FAILURE => '✘',
            Result::SKIPPED => '○',
            Result::INCOMPLETE => '○'
        ];

        $icon = $icon_map[$status];

        $preamble = "$icon " . $index . ') ';
        $preamble = pad_right($indent_width, $preamble, " ");

        return tag($style, $preamble) . $name;
    }

    public function onTestRunComplete(Event $event)
    {
        $summary = [
            tag("success", "Passed:"),
            "{$event->result_set->totalSuccesses()} of {$event->result_set->totalTests()}",
            tag("skipped", "Skipped:"),
            "{$event->result_set->totalSkipped()}",
            tag("failure", "Failed:"),
            "{$event->result_set->totalFailures()}",
            tag("bold", "Assertions:"),
            "{$event->result_set->totalAssertions()}"
        ];

        // The Passed / Failed / Skipped summary
        $summary = implode(" ", $summary);

        // Error formatting.
        $failures = $event->result_set->getFailures();
        $failure_count = count($failures);

        $index = 0;
        $result = [];
        foreach ($failures as $failure) {
            $index++;
            $result[] = tag("failure", pad_right(4, "$index )")."FAILURE: ". $failure->getBlock()->path());
            $result[] = $this->formatFailure($index, $failure);
            $result[] = "";
        }

        $result[] = $summary;

        return implode("\n", $result);
    }

    public function onTestStart(Event $event)
    {
        $this->test_count++;
    }

    public function onSuiteStart(Event $event)
    {
        $label = "Running: ".$event->suite->path();

        return tag("suite", $label."\n");
    }

    public function onSuiteComplete(Event $event)
    {
        return "";
    }

    public function onDescribeStart(Event $event)
    {
        $name = $event->describe->getName();
        $indent_width = ($event->describe->depth() - 1) * $this->options['indent'];
        return indent($indent_width, "<bold>$name </bold>", $this->options['indent']);
    }

    public function onDescribeComplete(Event $event)
    {
        if ($event->result->isFailure()) {
            $name = $event->describe->getName();
            $indent_width = ($event->describe->depth() - 1) * $this->options['indent'];
            return indent($indent_width, "<failure>Describe $name Failed</failure>", $this->options['indent']);
        }
    }

    // Formatting helpers
    // ##################

    protected function formatFailure($index, Result $failure)
    {
        $exception = $failure->getException();
        $exception_category = $failure->getException()->getCategory();

        return indent(4, implode(
            "\n",
            [
                tag("info", $exception_category.': ') . $exception->getMessage(),
                tag("info", "Via:"),
                $this->formatTrace($exception)
            ]
        ));
    }

    public function formatTrace(MaturaException $exception)
    {
        $index = 0;
        $result = [];
        $sliced_trace = array_slice($exception->originalTrace(), 0, $this->options['trace_depth']);

        foreach ($sliced_trace as $trace) {
            $index++;

            $parts = [pad_right(4, $index.")")];

            if (isset($trace['file'])) {
                $parts[] = $trace['file'].':'.$trace['line'];
            }
            if (isset($trace['function'])) {
                $parts[] = $trace['function'].'()';
            }
            $result[] = implode(' ', $parts);
        }

        return indent(3, implode("\n", $result));
    }

    /**
     * Conducts our 'event_group.action' => 'onEventGroupAction delegation'
     */
    public function renderEvent(Event $event)
    {
        $parts = array_map('ucfirst', array_filter(preg_split('/_|\./', $event->name)));
        $name = 'on'.implode($parts);

        if (is_callable([$this, $name])) {
            return call_user_func([$this, $name], $event);
        } else {
            return null;
        }
    }
}
