<?php namespace PSpec\Console\Output;

use PSpec\Core\Result;
use PSpec\Events\Event;
use PSpec\Exceptions\Exception as PSpecException;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Contains test rendering methods.
 */
class Printer
{
    protected $output;

    protected $options = [
        'trace_depth' => 7,
        'indent' => 3
    ];

    protected $test_count = 0;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;

        // move elsewhere...
        $output->getFormatter()->setStyle(
            'success',
            new OutputFormatterStyle('green')
        );

        $output->getFormatter()->setStyle(
            'failure',
            new OutputFormatterStyle('red')
        );

        $output->getFormatter()->setStyle(
            'info',
            new OutputFormatterStyle('blue')
        );

        $output->getFormatter()->setStyle(
            'skipped',
            new OutputFormatterStyle('yellow')
        );

        $output->getFormatter()->setStyle(
            'incomplete',
            new OutputFormatterStyle('yellow')
        );

        $output->getFormatter()->setStyle(
            'u',
            new OutputFormatterStyle(null, null, ['underscore'])
        );

        $output->getFormatter()->setStyle(
            'suite',
            new OutputFormatterStyle('yellow', null)
        );

        $output->getFormatter()->setStyle(
            'bold',
            new OutputFormatterStyle('blue', null)
        );
    }

    public function onTestComplete(Event $event)
    {
        $status = $event->result->getStatus();

        $icon_map = [
            Result::SUCCESS => '.',
            Result::FAILURE => 'F',
            Result::SKIPPED => 'X',
            Result::INCOMPLETE => 'X'
        ];

        $this->output->write($icon_map[$status]);
    }

    public function onTestRunStart()
    {
        $this->output->writeln('Running:');
    }

    public function onTestRunComplete(Event $event)
    {
        // Error formatting.
        $failures = $event->result_set->getFailures();

        $index = 0;
        $result = [];
        foreach ($failures as $failure) {
            $index++;
            $result[] = self::tag("failure", str_pad("$index ) FAILURE: " . $failure->getBlock()->path(), 4, ' ', STR_PAD_RIGHT));
            $result[] = $this->formatFailure($failure);
        }

        $this->output->writeln('');

        $this->output->writeln($result);

        $summary = [
            self::tag("success", "Passed:"),
            "{$event->result_set->totalSuccesses()} of {$event->result_set->totalTests()}",
            self::tag("skipped", "Skipped:"),
            "{$event->result_set->totalSkipped()}",
            self::tag("failure", "Failed:"),
            "{$event->result_set->totalFailures()}",
            self::tag("bold", "Assertions:"),
            "{$event->result_set->totalAssertions()}"
        ];

        $this->output->writeln(implode(" ", $summary));
    }

    public function onTestStart(Event $event)
    {
        ++$this->test_count;
    }

    protected function formatFailure(Result $failure)
    {
        $exception = $failure->getException();
        $exception_category = $failure->getException()->getCategory();

        return self::indent(4, implode(
            "\n",
            [
                self::tag("info", $exception_category.': ') . $exception->getMessage(),
                self::tag("info", "Via:"),
                $this->formatTrace($exception)
            ]
        ));
    }

    protected function formatTrace(PSpecException $exception)
    {
        $index = 0;
        $result = [];
        $sliced_trace = array_slice($exception->originalTrace(), 0, $this->options['trace_depth']);

        foreach ($sliced_trace as $trace) {
            $index++;

            $parts = [str_pad($index . ')', 4, ' ', STR_PAD_RIGHT)];

            if (isset($trace['file'])) {
                $parts[] = $trace['file'].':'.$trace['line'];
            }
            if (isset($trace['function'])) {
                $parts[] = $trace['function'].'()';
            }
            $result[] = implode(' ', $parts);
        }

        return self::indent(3, implode("\n", $result));
    }

    private static function indent($level, $string)
    {
        if (empty($string)) {
            return '';
        }

        $indent = str_repeat(" ", $level*1);

        return $indent.implode(explode("\n", $string), "\n".$indent);
    }

    private static function tag($tag)
    {
        $rest = array_slice(func_get_args(), 1);
        $text = implode($rest);

        return "<$tag>$text</$tag>";
    }
}
