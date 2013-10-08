<?php

namespace TurboCMS;

class Parser
{
    private $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    private function getFile()
    {
        return file_get_contents($this->settings["layout"]);
    }

    public function getKeys()
    {
        $keys = array();
        $pattern = "/\{{.*?\}}/";
        $file = $this->getFile();
        preg_match_all($pattern, $file, $matches);

        if (false === $matches) {
            return $keys;
        }

        $values = $this->readValues();
        for ($i = 0; $i < count($matches[0]); $i++) {
            $key = $this->filterKey($matches[0][$i]);
            $keys[$key] = ($values) ? $values[$key] : "";
        }

        return $keys;
    }

    private function filterKey($match)
    {
        $key = str_replace("{", "", $match);
        $key = str_replace("}", "", $key);

        return $key;
    }

    public function createFile($postVars)
    {
        $file = $this->replaceKeys($postVars);
        $this->saveValues($postVars);
        $this->writeFile($file);
    }

    private function saveValues($postVars)
    {
        $handle = fopen("core/key_values.json", "w+");
        fputs($handle, json_encode($postVars));
        fclose($handle);
    }

    private function readValues()
    {
        if (file_exists("core/key_values.json")) {
            $contents = file_get_contents("core/key_values.json");
            return json_decode($contents, true);
        } else {
            return false;
        }
    }

    public function replaceKeys($keys)
    {
        $file = $this->getFile();
        foreach ($keys as $key => $value) {
            $file = $this->doReplaceKey($key, $value, $file);
        }

        return $file;
    }

    private function doReplaceKey($key, $value, $file)
    {
        return str_replace("{{{$key}}}", $value, $file);
    }

    private function writeFile($fileContents)
    {
        $handle = fopen("core/views/index.html", "w+");
        fputs($handle, $fileContents);
        fclose($handle);
    }
}