<?php
error_reporting(E_ERROR);
$trueText = "";
$name = "";
$length = 0;
$size = 0;
$speed = 0;
$prefix = "bytes";
$postfix = "s";

function rtf_isPlainText($s) {
  $arrfailAt = array("*", "fonttbl", "colortbl", "datastore", "themedata");
  for ($i = 0; $i < count($arrfailAt); $i++)
      if (!empty($s[$arrfailAt[$i]])) return false;
  return true;
}
  if(isset($_FILES['filesubmit'])) {
    if (($_FILES['filesubmit']['type']) == 'text/plain') {
      $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      $rand_dir_name = substr(str_shuffle($chars), 0, 20);
      mkdir("Assets/Temp/$rand_dir_name");

      move_uploaded_file(@$_FILES["filesubmit"]["tmp_name"], "Assets/Temp/$rand_dir_name/".$_FILES["filesubmit"]["name"]);
      $text = file_get_contents("Assets/Temp/$rand_dir_name/".$_FILES["filesubmit"]["name"]);
      $name = $_FILES["filesubmit"]["name"];

      unlink("Assets/Temp/$rand_dir_name/".$_FILES["filesubmit"]["name"]);
      rmdir("Assets/Temp/$rand_dir_name/");

    if (!strlen($text))
      return "";

    $trueText = "";
    $stack = array();
    $j = -1;
    for ($i = 0, $len = strlen($text); $i < $len; $i++) {
        $c = $text[$i];

        switch ($c) {

            case "\\":

                $nc = $text[$i + 1];

                if ($nc == '\\' && rtf_isPlainText($stack[$j])) $trueText .= '\\';
                elseif ($nc == '~' && rtf_isPlainText($stack[$j])) $trueText .= ' ';
                elseif ($nc == '_' && rtf_isPlainText($stack[$j])) $trueText .= '-';

                elseif ($nc == '*') $stack[$j]["*"] = true;

                elseif ($nc == "'") {
                    $hex = substr($text, $i + 2, 2);
                    if (rtf_isPlainText($stack[$j]))
                        $trueText .= html_entity_decode("&#".hexdec($hex).";");

                    $i += 2;

                } elseif ($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z') {
                    $word = "";
                    $param = null;

                    for ($k = $i + 1, $m = 0; $k < strlen($text); $k++, $m++) {
                        $nc = $text[$k];
                        if ($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z') {
                            if (empty($param))
                                $word .= $nc;
                            else
                                break;
                        } elseif ($nc >= '0' && $nc <= '9')
                            $param .= $nc;

                        elseif ($nc == '-') {
                            if (empty($param))
                                $param .= $nc;
                            else
                                break;
                        } else
                            break;
                    }

                    $i += $m - 1;

                    $toText = "";
                    switch (strtolower($word)) {
                        case "u":
                            $toText .= html_entity_decode("&#x".dechex($param).";");
                            $ucDelta = @$stack[$j]["uc"];
                            if ($ucDelta > 0)
                                $i += $ucDelta;
                        break;
                        // Select line feeds, spaces and tabs.
                        case "par": case "page": case "column": case "line": case "lbr":
                            $toText .= "\n";
                        break;
                        case "emspace": case "enspace": case "qmspace":
                            $toText .= " ";
                        break;
                        case "tab": $toText .= "\t"; break;

                        case "chdate": $toText .= date("m.d.Y"); break;
                        case "chdpl": $toText .= date("l, j F Y"); break;
                        case "chdpa": $toText .= date("D, j M Y"); break;
                        case "chtime": $toText .= date("H:i:s"); break;

                        case "emdash": $toText .= html_entity_decode("&mdash;"); break;
                        case "endash": $toText .= html_entity_decode("&ndash;"); break;
                        case "bullet": $toText .= html_entity_decode("&#149;"); break;
                        case "lquote": $toText .= html_entity_decode("&lsquo;"); break;
                        case "rquote": $toText .= html_entity_decode("&rsquo;"); break;
                        case "ldblquote": $toText .= html_entity_decode("&laquo;"); break;
                        case "rdblquote": $toText .= html_entity_decode("&raquo;"); break;

                        default:
                            $stack[$j][strtolower($word)] = empty($param) ? true : $param;
                        break;
                    }
                    if (rtf_isPlainText($stack[$j]))
                        $trueText .= $toText;
                }

                $i++;
            break;

            case "{":
                array_push($stack, $stack[$j++]);
            break;

            case "}":
                array_pop($stack);
                $j--;
            break;

            case '\0': case '\r': case '\f': case '\n': break;
            default:
                if (rtf_isPlainText($stack[$j]))
                    $trueText .= $c;
            break;
        }
      }
      $length = strlen($trueText);
      $size = $length;
      if ($size > 1000) {
        $prefix = "KB";
      }
      if ($size > 1000000) {
        $prefix = "MB";
      }
      if ($size > 1000000000) {
        $prefix = "GB";
      }

    }
  }

  if (isset($_POST['wifi-speed'])) {
    $speedType = $_POST['wifi-speed'];
    $latency = $_POST['latency'];
    if (isset($_POST['text'])) {
      if (strlen($_POST['text'] != 0)) {
        echo("carl");
        $trueText = $_POST['text'];
        $length = strlen($trueText);
        $size = $length;
        if ($size > 1000) {
          $prefix = "KB";
          $size /= 1000;
        }
        if ($size > 1000000) {
          $prefix = "MB";
          $size /= 1000000;
        }
        if ($size > 1000000000) {
          $prefix = "GB";
          $size /= 1000000000;
        }
      }
    }

    switch ($speedType) {
      case 'wifi':
          $speed = $size*1000 / 30000000;
          $postfix = "ms";
          if ($latency == "yes") {
            $speed += 2;
          }

        break;

      case 'dsl':
          $speed = $size*1000 / 2000000;
          $postfix = "ms";
          if ($latency == "yes") {
            $speed += 5;
          }
        break;

      case 'r4g':
          $speed = $size*1000 / 4000000;
          $postfix = "ms";
          if ($latency == "yes") {
            $speed += 20;
          }
        break;

      case 'g3g':
          $speed = $size*1000 / 1000000;
          $postfix = "ms";
          if ($latency == "yes") {
            $speed += 40;
          }
        break;

      case 'r3g':
          $speed = $size*1000 / 750000;
          $postfix = "ms";
          if ($latency == "yes") {
            $speed += 100;
          }
        break;

      case 'g2g':
          $speed = $size*1000 / 450000;
          $postfix = "ms";
          if ($latency == "yes") {
            $speed += 150;
          }
        break;

      case 'r2g':
          $speed = $size*1000 / 250000;
          $postfix = "ms";
          if ($latency == "yes") {
            $speed += 300;
          }
        break;

      case 'gprs':
          $speed = $size*1000 / 50000;
          $postfix = "ms";
          if ($latency == "yes") {
            $speed += 500;
          }
        break;

      default:
        $speed = "No Throttle";
        break;
    }

    if ($speed > 1000) {
      $speed = $speed / 1000;
      $prefix = "s";
    }
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link href='https://fonts.googleapis.com/css?family=Ubuntu+Mono:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="Assets/Styles/CSS/main.css" media="screen" title="no title" charset="utf-8">
    <title>Hydrogen</title>
  </head>
  <body onload="draw('<?php if ($speed != "No Throttle") { echo $speed; } ?>')">
    <header class="head">
      <nav class="navbar">
        <div class="nav-logo">
          <img src="Assets/Images/logo.png" alt="logo" />
        </div>
        <a class="navlink" href="#results">Results</a>
        <a class="navlink" href="#submit">Submit</a>
        <a class="navlink" href="#about">About</a>
      </nav>

      <h1 class="hero-title">Hydrogen.io</h1>
      <p class="kicker">Size // Length // Speed</p>

    </header>

    <section class="about-section">
      <h3 id="about" class="section-header">About</h3>
      <div class="container">
        <div class="about-image">
          <img class="body-image" src="Assets/Images/Hydrogen-image.png" alt="hydrogen pic" />
        </div>
        <div class="about-text">
          <h2>About</h2>
          <p class="text">    This is a simple tool that allows you to find the length of you text trueTexts and to see the byte size of your text. This is useful for front end developers.</p>
          <h2>How To </h2>
          <p class="text">    In order to use this simple application you can either submit a <i>.txt</i> file. Or you can fill in the text area with text. This will then give you the amount of characters in the text, the size, and the amount of time to load based of latency and speed.</p>
        </div>
      </div>
    </section>

    <section id="submit" class="submit-section">
      <h3 id="submit" class="section-header">Submit</h3>

      <form class="submit-file" action="index.php#results" enctype="multipart/form-data" method="post">

        <label class="input-file-label">Input File Here</label> <br>
        <input class="file" type="file" name="filesubmit"/><br>

        <h3 class="input-file-label">- Or -</h3>

        <label>Input Text</label> <br>
        <textarea class="area" name="text"></textarea>

        <h3>- Latency -</h3>

        <label>Select Wifi Speed</label> <br>

        <select id="2" class="speed" name="wifi-speed">
          <option value="no-throttle">No Throttle</option>
          <option value="wifi">WiFi (30 Mb/s 2ms RTT)</option>
          <option value="dsl">DSL (2 Mb/s 5ms RTT)</option>
          <option value="r4g">Regular 4G (4 Mb/s 20ms RTT)</option>
          <option value="g3g">Good 3G (1 Mb/s 40ms RTT)</option>
          <option value="r3g">Regular 3G (750 Kb/s 100ms RTT)</option>
          <option value="g2g">Good 2G (450 Kb/s 150ms RTT) </option>
          <option value="r2g">Regular 2G (250 Kb/s 300ms RTT)</option>
          <option value="gprs">GPRS (50 Kb/s 500ms RT)</option>
        </select> <br>
        <label>Add Latency</label>
        <input type="checkbox" checked="true" name="latency" value="yes"><br>

        <input class="submit" type="submit" name="submit"/>
      </form>
    </section>

    <section id="results" class="results">

      <h3 id="#results" class="section-header">Results</h3>
      <div class="results-container">
        <h3>Results for <?php print($name); ?></h5>
        <div class="length">
          <h4 class="length-header sub-header">Length</h4>
          <?php print('<span class="result">'.$length.' chars</span>') ?>
        </div>
        <div class="size">
          <h4 class="size-header sub-header">Size</h4>
          <?php print('<span class="result">'.$size.' '.$prefix.'</span>') ?>
        </div>
        <div class="speed">
          <h4 class="speed-header sub-header">Speed</h4>
          <?php print('<span class="result ">'.$speed.''.$postfix.'</span>') ?>
        </div>
        <div class="original-text">
          <h4 class="orginal-header sub-header">Original Text</h4>
          <?php print('<p class="ori-text">'.$trueText.'</p>'); ?>
        </div>
      </div>
      <div class="canvas-container">
        <h3>Text Speedometer</h3>
        <canvas id="tutorial" width="440" height="220">Canvas not available.</canvas>
        <p>
          This speedomter shows the speed of your text loading onto the page
        </p>
      </div>
    </section>

    <footer class="foot">
      <nav class="navbar">
        <div class="nav-logo">
          <img src="Assets/Images/logo.png" alt="logo" />
        </div>
        <a class="navlink" href="#results">Results</a>
        <a class="navlink" href="#submit">Submit</a>
        <a class="navlink" href="#about">About</a>
      </nav>
      <div class="copy-right">
        <p>Copyright © 2015</p>
      </div>
    </footer>
    <script src="Assets/Scripts/JS/jquery.js"></script>
    <script src="Assets/Scripts/JS/functions.js"></script>
    <script src="Assets/Scripts/JS/speedometer.js"></script>
  </body>
</html>
