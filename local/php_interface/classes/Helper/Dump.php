<?php

/* Если стоит false, то будет отображаться тип данных */
const LOGGER_HIDE_TYPES = false;

/* Размер шрифта, табуляция и тд */
const LOGGER_FONT_SIZE = 13;
const LOGGER_LINE_HEIGHT = 2;
const LOGGER_SPACES_COUNT = 4;

/* Темы, просто надо раскомментировать */
/* colors light */;
//const LOGGER_BACKGROUND = "#FFF";
//const LOGGER_FONT_WEIGHT = "400";
//const LOGGER_TEXT_BACKGROUND = "#0F4C5C";
//const LOGGER_HEADER_BACKGROUND = "#F1FAEE";
//const LOGGER_KEY_COLOR = "#0F4C5C";
//const LOGGER_STRING_COLOR = "#E36414";
//const LOGGER_INTEGER_COLOR = "#9A031E";
//const LOGGER_FLOAT_COLOR = "#9A031E";
//const LOGGER_BOOL_COLOR = "#9A031E";
//const LOGGER_TYPES_COLOR = "#8D99AE";
//const LOGGER_NULL_COLOR = "#0000FF";

/* colors dark */;
const LOGGER_BACKGROUND = "#2e2e2e";
const LOGGER_FONT_WEIGHT = "400";
const LOGGER_TEXT_BACKGROUND = "#e5b567";
const LOGGER_HEADER_BACKGROUND = "#2e2e2e";
const LOGGER_KEY_COLOR = "#e5b567";
const LOGGER_STRING_COLOR = "#b4d273";
const LOGGER_INTEGER_COLOR = "#e87d3e";
const LOGGER_FLOAT_COLOR = "#9e86c8";
const LOGGER_BOOL_COLOR = "#b05279";
const LOGGER_TYPES_COLOR = "#6c99bb";
const LOGGER_NULL_COLOR = "#0000FF";

/* colors pizdec vyribay */;
//const LOGGER_BACKGROUND = "#000000";
//const LOGGER_TEXT_BACKGROUND = "#89FC00";
//const LOGGER_FONT_WEIGHT = "700";
//const LOGGER_HEADER_BACKGROUND = "#000000";
//const LOGGER_KEY_COLOR = "#89FC00";
//const LOGGER_STRING_COLOR = "#F0F600";
//const LOGGER_INTEGER_COLOR = "#FF0000";
//const LOGGER_FLOAT_COLOR = "#F5B700";
//const LOGGER_BOOL_COLOR = "#04E762";
//const LOGGER_TYPES_COLOR = "#FF00BF";
//const LOGGER_NULL_COLOR = "#0000FF";

class Dump
{

    public static function pr($data, $die = false, $forAll = false,$backtrace = false)
    {
        self::setOptons();

        global $USER;
        if (!isset($USER) || (!$USER->isAdmin() && !$forAll))return;

        echo "<div class='logger_wrapper'>";

        $dbt = debug_backtrace();
        $file = $dbt[0]['file'];
        $line = $dbt[0]['line'];

        ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
        <div class="logger_top">
            <div>
                <strong>File:</strong> <?= $file ?> [Line: <?= $line ?>]
            </div>
            <div class="logger_minimize" onclick="minimize_dump(this)">
                <svg xmlns="http://www.w3.org/2000/svg" height="36px" viewBox="0 0 24 24" width="36px" fill="<?=LOGGER_KEY_COLOR?>">
                <path d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 8l-6 6 1.41 1.41L12 10.83l4.59 4.58L18 14z"/></svg>
            </div>

        </div>

        <div class="logger_body">
        <?php

        if ($backtrace) {
            foreach ($dbt as $key => $item) {
                unset($dbt[$key]['args']);
            }

            echo "Backtrace ";
            echo self::prepare_array($dbt);
            echo "<br>";
            echo "<hr class='logger_obj_divider'>";
        }

        if (is_array($data)) {
            echo "array(".count($data).") ";
            echo self::prepare_array($data);

        } elseif (is_string($data)) {

            echo '<div class="logger_container">';
            echo self::prepare_str($data);
            echo '</div>';

        } elseif (is_int($data)) {

            echo '<div class="logger_container">';
            echo self::prepare_int($data);
            echo '</div>';

        } elseif (is_float($data)) {

            echo "float";
            echo '<div class="logger_container">';
            echo self::prepare_float($data);
            echo '</div>';

        } elseif (is_bool($data)) {

            echo '<div class="logger_container">';
            echo self::prepare_bool($data);
            echo '</div>';

        } elseif (is_object($data)) {
            echo self::prepare_obj($data);

        } elseif (is_null($data)) {

            echo self::prepare_null();

        }
        echo '</div>';
        echo '</div>';


        if ($die) {
            die();
        }
    }

    private static function prepare_array($arr):string
    {

        $str = '[<div class="logger_container">';

        foreach ($arr as $key => $value) {

            $str .= '<div class="logger_container roll_up">';

            if (is_array($value)) {

                $str .= '<div class="logger_arrow" onclick="minimize_array(this)">&#9660;</div>';
                $str .= "<span class='logger_key'>" . self::prepare_key($key) . "</span> " .
                    "<span class='logger_counter'>array(" . count($value) . ")</span> => " . self::prepare_array($value);

            } elseif (is_int($value)) {

                $str .= "<span class='logger_key'>" . self::prepare_key($key) . "</span> " . self::prepare_int($value);

            } elseif (is_float($value)) {

                $str .= "<span class='logger_key'>" . self::prepare_key($key) . "</span> " . self::prepare_float($value);

            } elseif (is_string($value)) {

                $str .= "<span class='logger_key'>" . self::prepare_key($key) . "</span> " . self::prepare_str($value);

            } elseif (is_bool($value)) {

                $str .= "<span class='logger_key'>" . self::prepare_key($key) . "</span> " . self::prepare_bool($value);

            } elseif (is_object($value)) {
                $str .= "<hr class='logger_obj_divider'>";
                $str .= "<span class='logger_key'>" . self::prepare_key($key) . "</span> " . self::prepare_obj($value,true);
                $str .= "<hr class='logger_obj_divider'>";
            } elseif (is_null($value)) {

                $str .= "<span class='logger_key'>" . self::prepare_key($key) . "</span> " . self::prepare_null();
            }

            $str .= '</div>';

        }

        $str .= '</div>]';

        return $str;
    }

    private static function prepare_str($str):string
    {
        return "<span class='logger_counter'>string(" . strlen($str) . ")</span>  => " . "<span class='logger_string'>" . strip_tags($str) . "</span>";
    }

    private static function prepare_int($int):string
    {
        return "<span class='logger_counter'>int</span>  => " . "<span class='logger_integer'>" . $int . "</span>";
    }

    private static function prepare_float($float):string
    {
        return "<span class='logger_counter'>float</span>  => " . "<span class='logger_float'>" . $float . "</span>";
    }

    private static function prepare_bool($bool):string
    {
        $b = $bool ? "true" : 'false';
        return "<span class='logger_counter'>bool</span>  => " . "<span class='logger_bool'>" . $b . "</span>";
    }

    private static function prepare_null():string
    {
        return "<span class='logger_counter'>null</span>  => " . "<span class='logger_null'>NULL</span>";
    }

    private static function prepare_key($key):string
    {
        if (is_int($key)) {
            return "[" . $key . "]";
        } else {
            return '["' . $key . '"]';
        }
    }

    private static function prepare_obj($obj,$nullObject=false):string
    {
        $arr = [];
        $arr['name'] = get_class($obj);
        $arr['methods'] = get_class_methods($obj);
        $arr['parent'] = get_parent_class($obj);

        if(!$nullObject){
            $arr['props'] = get_object_vars($obj);
            $arr['vars'] = get_class_vars(get_class($obj));
        }

        $str = '<span class="logger_key">';
        $str .= '</span>';
        $str .= "<div class='logger_container'>";
        $str .= '<div class="logger_arrow" onclick="minimize_array(this)">&#9660;</div>';
        $str .= '<i>Instance of class <strong>' . $arr['name'] . '</strong></i> ';

        $str .= self::prepare_array($arr);
        $str .= '</div>';
        return $str;
    }

    private static function getStyles(){
        $LOGGER_HIDE_TYPES = '';
        if (LOGGER_HIDE_TYPES) {
             $LOGGER_HIDE_TYPES = "display: none;";
        }

        echo '
        <style>
            .logger_container {
                font-size: '.LOGGER_FONT_SIZE.'px;
                margin-left: '.LOGGER_SPACES_COUNT * 0.3.'em;
                font-family: "JetBrains Mono", monospace;
                line-height: '.LOGGER_LINE_HEIGHT.'em;
            }
        
            .logger_minimize {
                cursor: pointer;
            }
        
            .logger_wrapper {
                margin: 15px;
                border: 1px dashed black;
                padding-bottom: 15px;
                background-color: '.LOGGER_BACKGROUND.';
                color: '.LOGGER_TEXT_BACKGROUND.';
                font-weight: '.LOGGER_FONT_WEIGHT.';
                font-size: '.LOGGER_FONT_SIZE.'px;
                font-family: "JetBrains Mono", monospace;
            }
        
            .logger_body {
                padding: 0 15px;
            }
        
            .logger_top {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: '.LOGGER_HEADER_BACKGROUND.';
                padding: 10px 15px;
                margin-bottom: 15px;
        
            }
        
            .logger_arrow {
                position: absolute;
                margin-left: -13px;
                cursor: pointer;
            }
        
            .logger_string {
                color: '.LOGGER_STRING_COLOR.';
            }
        
            .logger_key {
                color: '.LOGGER_KEY_COLOR.';
            }
        
            .logger_integer {
                color: '.LOGGER_INTEGER_COLOR.';
                font-weight: bold;
            }
        
            .logger_float {
                color: '.LOGGER_FLOAT_COLOR.';
            }
        
            .logger_null {
                color: '.LOGGER_NULL_COLOR.';
                font-style: italic;
            }
        
        
            .logger_bool {
                color: '.LOGGER_BOOL_COLOR.';
                font-style: italic;
                font-weight: bold;
            }
        
            .logger_counter {
                color: '.LOGGER_TYPES_COLOR.';
                '.$LOGGER_HIDE_TYPES.'
            }
        
            .logger_obj_divider {
                opacity: 0.4;
                height: 0;
                border-bottom: none;
                margin-right: 15px;
            }
        </style>
        ';
    }
    protected static function getScripts(){
        echo '<script>
            function isFunction(functionToCheck)  {
                let getType = {};
                return functionToCheck && getType.toString.call(functionToCheck) === "[object Function]";
            }

            if (!isFunction("minimize_dump")) {

                function minimize_dump(e) {

            let header = e.parentElement;
            let wrapper = header.parentElement;
                    let body = wrapper.querySelector(".logger_body");
                    body.style.display = body.style.display === "none" ?"block": "none";
                    let rotation = body.style.display === "none" ? 180 : 0;
                    e.style.transform = "rotate(" + rotation + "deg)";
                }
            }

            if (!isFunction("minimize_array")) {
                function minimize_array (e) {
                    let parent = e.parentElement;
                    let inner = parent.querySelectorAll(".logger_container")
                    e.innerHTML = inner[0].style.display === "none" ? "&#9660;" : "&#9658;";
        
                    inner.forEach((line) => {
                        line.style.display = line.style.display === "none"?"block" : "none";
                    })
                }
            }
        </script>';
    }

    protected static function setOptons(){
        self::getStyles();
        self::getScripts();
    }

    public static function console($data)
    {
        $dbt = debug_backtrace();
        $res['dbt']['file'] = $dbt[0]['file'];
        $res['dbt']['line'] = $dbt[0]['line'];
        $res['data'] = $data;

        $json = json_encode($res); ?>
        <script>
            let json = '<?=$json?>'
            console.log(JSON.parse(json))
        </script>
        <?php

    }

}