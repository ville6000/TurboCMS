<?php

namespace TurboCMS;

/**
 * Class Parser
 *
 * @package TurboCMS
 */
class Parser
{
    /**
     * TurboCMS settings
     * @var array $settings
     */
    private $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get layout file contents
     *
     * @return string
     */
    private function getFile()
    {
        return file_get_contents($this->settings["layout"]);
    }

    /**
     * Load layout file and get placeholder keys.
     * Previously saved values are returned as key values.
     *
     * @return array
     */
    public function getKeys()
    {
        $fileContent = $this->getFile();
        $existingValues = $this->readValues();
        
        return $this->getKeysFromContent($fileContent, $existingValues);
    }

    /**
     * Get placeholder keys from content in parameters.
     *
     * @param $content Text content (from layout file) to get keys from
     * @param $existingValues Current keys (in layout file), merged with new keys
     *
     * @return array
     *
     */
    public function getKeysFromContent($content = '', $existingValues = array())
    {
        $keys = array();
        $pattern = "/\{{.*?\}}/";
        $matches = false;
        preg_match_all($pattern, $content, $matches);

        // Layout file didn't contain any keys
        if (false === $matches) {
            return $keys;
        }

        // Find previously saved values and set them as key values.
        for ($i = 0; $i < count($matches[0]); $i++) {
            $key = $this->filterKey($matches[0][$i]);
            $keys[$key] = ($existingValues) ? $existingValues[$key] : "";
        }

        return $keys;
    }

    /**
     * Remove curly braces from placeholder string
     *
     * @param $match
     *
     * @return mixed
     */
    private function filterKey($match)
    {
        $key = str_replace("{", "", $match);
        $key = str_replace("}", "", $key);

        return $key;
    }

    /**
     * Create layout file
     *
     * @param $postVars
     */
    public function createFile($postVars)
    {
        $file = $this->replaceKeys($postVars);
        $this->saveValues($postVars);
        $this->writeFile($file);
    }

    /**
     * Save placeholder values to json file
     *
     * @param $postVars
     */
    private function saveValues($postVars)
    {
        $handle = fopen("core/key_values.json", "w+");
        fputs($handle, json_encode($postVars));
        fclose($handle);
    }

    /**
     * Read previously saved values from json file.
     *
     * @return bool|mixed
     */
    private function readValues()
    {
        if (file_exists("core/key_values.json")) {
            $contents = file_get_contents("core/key_values.json");
            return json_decode($contents, true);
        } else {
            return false;
        }
    }

    /**
     * Replace placeholder keys with values
     *
     * @param $keys
     *
     * @return mixed|string
     */
    public function replaceKeys($keys)
    {
        $file = $this->getFile();
        foreach ($keys as $key => $value) {
            $file = $this->doReplaceKey($key, $value, $file);
        }

        return $file;
    }

    /**
     * Replace placeholder key with value
     *
     * @param $key
     * @param $value
     * @param $file
     *
     * @return mixed
     */
    private function doReplaceKey($key, $value, $file)
    {
        return str_replace("{{{$key}}}", $value, $file);
    }

    /**
     * Create layout file
     *
     * @param $fileContents
     */
    private function writeFile($fileContents)
    {
        $handle = fopen("core/views/index.html", "w+");
        fputs($handle, $fileContents);
        fclose($handle);
    }
}
