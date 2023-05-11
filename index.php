<html xmlns="http://www.w3.org/1999/xhtml">
<head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Unang..</title>
<style type="text/css">
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: .9em;
        color: #000000;
        background-color: #FFFFFF;
        margin: 0;
        padding: 10px 20px 20px 20px;
    }

    samp {
        font-size: 1.3em;
    }

    a {
        color: #000000;
        background-color: #FFFFFF;
    }

    sup a {
        text-decoration: none;
    }

    hr {
        margin-left: 90px;
        height: 1px;
        color: #000000;
        background-color: #000000;
        border: none;
    }

    #logo {
        margin-bottom: 10px;
        margin-left: 28px;
    }

    .text {
        width: 80%;
        margin-left: 90px;
        line-height: 140%;
    }
</style>
</head>

<body>
<h2>Yoonang...</h2>
    <p><img src="" id="logo" alt="Unang.." width="1" height="1" /></p>

<?php if ($str_language == 'en'): ?>

    <p class="text"><strong>Der virtuelle <span lang="en" xml:lang="en">Host</span> wurde erfolgreich eingerichtet.</strong></p>
    <p class="text">Wenn Sie diese Seite sehen, dann bedeutet dies, dass der neue virtuelle <span lang="en" xml:lang="en">Host</span> erfolgreich eingerichtet wurde. Sie können jetzt Ihren <span lang="en" xml:lang="en">Web</span>-Inhalt hinzufügen, diese Platzhalter-Seite<sup><a href="#footnote_1">1</a></sup> sollten Sie ersetzen <abbr title="beziehungsweise">bzw.</abbr> löschen.</p>
    <p class="text">
        Server-Name: <samp><?php echo $_SERVER['SERVER_NAME']; ?></samp><br />
        Document-Root: <samp><?php echo $_SERVER['DOCUMENT_ROOT']; ?></samp><br />
        Protokoll: <samp>
        <?php
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                echo 'https';
            } else {
                echo 'http';
            }
        ?>
        </samp>
    </p>
    <p class="text" id="footnote_1"><small><sup>1</sup> Dateien: <samp>index.php</samp> und <samp>MAMP-PRO-Logo.png</samp></small></p>
    <hr />
    <p class="text">This page in: <?php echo $str_available_languages; ?></p>

<?php elseif ($str_language == 'en'): ?>

    <p class="text"><strong>The virtual host was set up successfully.</strong></p>
    <p class="text">If you can see this page, your new virtual host was set up successfully. Now, web content can be added and this placeholder page<sup><a href="#footnote_1">1</a></sup> should be replaced or deleted.</p>
    <p class="text">
        Server name: <samp><?php echo $_SERVER['SERVER_NAME']; ?></samp><br />
        Document root: <samp><?php echo $_SERVER['DOCUMENT_ROOT']; ?></samp><br />
        Protocol: <samp>
        <?php
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                echo 'https';
            } else {
                echo 'http';
            }
        ?>
        </samp>
    </p>
    <p class="text" id="footnote_1"><small><sup>1</sup> Files: <samp>index.php</samp> and <samp>MAMP-PRO-Logo.png</samp></small></p>
    <hr />
    <p class="text">Diese Seite auf: <?php echo $str_available_languages; ?></p>

<?php endif; ?>

</body>
</html>
