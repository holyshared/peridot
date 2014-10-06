<?php
namespace Peridot\Runner;
use Peridot\Core\Spec;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;

/**
 * Class Context tracks the state of the runner - i.e the current suite
 * @package Peridot\Runner
 */
class Context
{
    /**
     * @var array
     */
    protected $suites;

    /**
     * @var Context
     */
    private static $instance = null;

    /**
     * Private constructor
     */
    private function __construct()
    {
        $this->suites = [new Suite("", function() {})];
    }

    /**
     * @return \Peridot\Core\Suite
     */
    public function getCurrentSuite()
    {
        return $this->suites[0];
    }

    /**
     * Creates a suite and sets it on the suite factory
     *
     * @param $description
     * @param callable $fn
     */
    public function describe($description, callable $fn, $pending = null)
    {
        $suite = new Suite($description, $fn);
        if (!is_null($pending)) {
            $suite->setPending($pending);
        }
        $this->getCurrentSuite()->addSpec($suite);
        array_unshift($this->suites, $suite);
        call_user_func($suite->getDefinition());
        array_shift($this->suites);
        return $suite;
    }

    /**
     * Create a spec and add it to the current suite
     *
     * @param $description
     * @param $fn
     */
    public function it($description, callable $fn, $pending = null)
    {
        $spec = new Spec($description, $fn);
        if (!is_null($pending)) {
            $spec->setPending($pending);
        }
        $this->getCurrentSuite()->addSpec($spec);
        return $spec;
    }

    /**
     * Add a setup function for all specs in the
     * current suite
     *
     * @param callable $fn
     */
    public function beforeEach(callable $fn)
    {
        $this->getCurrentSuite()->addSetUpFunction($fn);
    }

    /**
     * Add a tear down function for all specs in the current suite
     *
     * @param callable $fn
     */
    public function afterEach(callable $fn)
    {
        $this->getCurrentSuite()->addTearDownFunction($fn);
    }

    /**
     * Singleton access to SuiteFactory
     *
     * @return Context
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
