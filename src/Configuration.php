<?php
namespace Peridot;

/**
 * Configuration stores configured values used through the Peridot application
 * lifecycle.
 *
 * @package Peridot
 */
class Configuration
{
    /**
     * @var boolean
     */
    protected $colorsEnabled = true;

    /**
     * @var string
     */
    protected $grep = '*.spec.php';

    /**
     * @var string
     */
    protected $reporter = 'spec';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $configurationFile;

    /**
     * @var string
     */
    protected $dsl;

    /**
     * @var bool
     */
    protected $stopOnFailure = false;

    public function __construct()
    {
        $this->path = getcwd();
        $this->configurationFile = getcwd() . DIRECTORY_SEPARATOR . 'peridot.php';
        $this->dsl = __DIR__ . DIRECTORY_SEPARATOR . 'Dsl.php';
    }

    /**
     * Set the pattern used to load tests
     *
     * @param string $grep
     */
    public function setGrep($grep)
    {
        $this->grep = $grep;

        return $this;
    }

    /**
     * Returns the pattern used to load tests
     *
     * @return string
     */
    public function getGrep()
    {
        return $this->grep;
    }

    /**
     * Set the name of the reporter to use
     *
     * @param string $reporter
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Return the name of the reporter configured for use
     *
     * @return string
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set the path to load tests from
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Return the path being searched for tests
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Disable output colors
     *
     * @return $this
     */
    public function disableColors()
    {
        $this->colorsEnabled = false;

        return $this;
    }

    /**
     * Check if output colors are disabled
     *
     * @return boolean
     */
    public function areColorsEnabled()
    {
        return $this->colorsEnabled;
    }

    /**
     * Stop the suite runner when a failure occurs
     *
     * @return $this
     */
    public function stopOnFailure()
    {
        $this->stopOnFailure = true;

        return $this;
    }

    /**
     * Check if the suite runner should stop on failure
     *
     * @return bool
     */
    public function shouldStopOnFailure()
    {
        return $this->stopOnFailure;
    }

    /**
     * Set the path to a Peridot configuration file
     *
     * @param string $configurationFile
     */
    public function setConfigurationFile($configurationFile)
    {
        $search = [$configurationFile, getcwd() . DIRECTORY_SEPARATOR . $configurationFile];
        $found = array_filter($search, 'file_exists');

        if (count($found) == 0) {
            throw new \RuntimeException("Configuration file specified but does not exist");
        }

        $this->configurationFile = $found[0];

        return $this;
    }

    /**
     * Return the path to the Peridot configuration file. Returns a relative
     * path if it exists, otherwise return the provided value
     *
     * @return string
     */
    public function getConfigurationFile()
    {
        return $this->configurationFile;
    }

    /**
     * Set the path to a DSL file for defining
     * the test language used
     *
     * @param string $dsl
     */
    public function setDsl($dsl)
    {
        $this->dsl = $dsl;
        return $this;
    }

    /**
     * Get the path to a DSL file containing
     * test functions to use
     *
     * @return string
     */
    public function getDsl()
    {
        return $this->dsl;
    }
}
