<?php

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

require_once('_bootstrap.php');

$finder = new Finder();
$fs = new Filesystem();

if (file_exists(OUTPUT_DIR)) {
    writeln('Removing existing directory');
    $fs->remove(OUTPUT_DIR);
}

writeln('Creating ' . OUTPUT_DIR);
$fs->mkdir(OUTPUT_DIR);

$files = $finder->name('*.html')->in(DOWNLOAD_DIR)->sortByName();

$toc = array();
$toc[] = 'JCR-283 Specification';
$toc[] = '=====================';
$toc[] = '';
$toc[] = '.. toctree::';
$toc[] = '   :maxdepth: 2';
$toc[] = '';


foreach ($files as $file) {
    $name = strstr(basename($file), '.', true);
    $toc[] = '    ' . $name;
    $outfile = OUTPUT_DIR . DIRECTORY_SEPARATOR . $name . '.rst';

    writeln(' -- Fixing: ' . $file);
    fixupfile($file);

    writeln(' -- Converting: ' . $file . ' to: ' .$outfile);
    exec('pandoc -s ' . $file . ' -o ' . $outfile);
}

writeln(' -- Writing TOC');
file_put_contents(OUTPUT_DIR . DIRECTORY_SEPARATOR . 'index.rst', implode("\n", $toc));

writeln('Copying images');

$finder = new Finder();
$files = $finder->name('*.png')->name('*.gif')->in(DOWNLOAD_DIR);

foreach ($files as $file) {
    writeln(' -- Copying: ' . basename($file));
    copy($file, OUTPUT_DIR . DIRECTORY_SEPARATOR . basename($file));
}

/**
 * Try and determine code blocks
 */
function fixupfile($file)
{
    $dom = new \DOMDocument(1.0);
    $dom->loadHtml(file_get_contents($file));
    $xpath = new \DOMXpath($dom);
    $items = $xpath->query('//font[@face="Courier New, monospace"]');
}
