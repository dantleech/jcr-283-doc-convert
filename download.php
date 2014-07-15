<?php

use Symfony\Component\Filesystem\Filesystem;

require_once(__DIR__ . '/_bootstrap.php');

$fs = new Filesystem();

const JCR_INDEX = 'http://www.day.com/maven/jcr/2.0/';


if (file_exists(DOWNLOAD_DIR)) {
    writeln('Removing existing directory');
    $fs->remove(DOWNLOAD_DIR);
}

writeln('Creating ' . DOWNLOAD_DIR);
$fs->mkdir(DOWNLOAD_DIR);

writeln('Pulling down index page');
$xml = new \DOMDocument(1.0);
@$xml->loadHtml(file_get_contents(JCR_INDEX));

$xpath = new \DOMXpath($xml);
$items = $xpath->query('//h4');

foreach ($items as $i => $item) {
    $fname = $item->firstChild->getAttribute('href');
    $chapterUrl = JCR_INDEX . $fname;
    writeln(' -- Getting: ' . $chapterUrl);

    $xml = new \DOMDocument(1.0);
    
    @$xml->loadHtml(file_get_contents($chapterUrl));

    if (preg_match('{^[0-9]_}', $fname)) {
        $fname = '0' . $fname;
    }

    writeln(' -- Writing: ' . $fname);
    $xml->saveHTMLFile(DOWNLOAD_DIR . DIRECTORY_SEPARATOR . $fname);

    $xpath = new \DOMXpath($xml);
    $images = $xpath->query('//img');

    foreach ($images as $img) {
        $imgName = $img->getAttribute('src');
        writeln('   -- Image: ' . $imgName);
        $image = file_get_contents(JCR_INDEX . $imgName);

        file_put_contents(DOWNLOAD_DIR . DIRECTORY_SEPARATOR . $imgName, $image);
    }
}
