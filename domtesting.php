<?php
// http://www.ultramegatech.com/blog/2009/07/generating-xhtml-documents-using-domdocument-in-php/
// Create document
/*
$doctype = new DOMImplementation;
$dtd = $doctype->createDocumentType('html',
                  '-//W3C//DTD XHTML 1.1//EN',
                  'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd');
 
$document = new DOMImplementation;
$document = $document->createDocument('http://www.w3.org/1999/xhtml',
                   'html',
                   $dtd);
// Create head element
$head = $document->createElement('head');
$metahttp = $document->createElement('meta');
$metahttp->setAttribute('http-equiv', 'Content-Type');
$metahttp->setAttribute('content', 'text/html; charset=utf-8');
$head->appendChild($metahttp);
 
$title = $document->createElement('title', 'DOMDocument');
$head->appendChild($title);
 
$css = $document->createElement('link');
$css->setAttribute('href', 'styles.css');
$css->setAttribute('rel', 'stylesheet');
$css->setAttribute('type', 'text/css');
$head->appendChild($css);
// Create body element
$body = $document->createElement('body');
 
// Wrapper div
$wrapper = $document->createElement('div');
$wrapper->setAttribute('id', 'wrapper');
// Header div
$header = $document->createElement('div');
$header->setAttribute('id', 'header');
$wrapper->appendChild($header);
 
// Header img
$himg = $document->createElement('img');
$himg->setAttribute('src', 'header.gif');
$himg->setAttribute('alt', 'Header');
$himg->setAttribute('width', '400');
$himg->setAttribute('height', '100');
$header->appendChild($himg);
// Nav div
$nav = $document->createElement('div');
$nav->setAttribute('id', 'nav');
$wrapper->appendChild($nav);
 
// Nav ul
$navlist = $document->createElement('ul');
$nav->appendChild($navlist);
$menuArray = array();
$menuArray['index.php'] = 'Home';
$menuArray['download.php'] = 'Download';
$menuArray['generate.php'] = 'Generate';
$menuArray['about.php'] = 'About';
 
$currentPage = basename($_SERVER['SCRIPT_FILENAME']);
 
foreach($menuArray as $page => $title) {
    $navitem = $document->createElement('li');
    if($page == $currentPage) {
        $navitem->setAttribute('class', 'active');
    }
 
    $navlink = $document->createElement('a', $title);
    $navlink->setAttribute('href', $page);
 
    $navitem->appendChild($navlink);
    $navlist->appendChild($navitem);
}
// Content div
$content = $document->createElement('div');
$content->setAttribute('id', 'content');
$wrapper->appendChild($content);
 
// Actual page content
$h1 = $document->createElement('h1', 'DOMDocument');
$content->appendChild($h1);
$body->appendChild($wrapper);
 
// Add head and body to document
$html = $document->getElementsByTagName('html')->item(0);
$html->appendChild($head);
$html->appendChild($body);
// Output document
$document->formatOutput = true;
echo $document->saveXML();
*/
$form = new DOMImplementation;
$form = $form->createDocument();

$field = $form->createElement('input');
$field->setAttribute('type', 'text');
$field->setAttribute('name', 'username');
$field->setAttribute('id', 'username');
$field->setAttribute('value', 'Mickey Mouse');
$p1 = $form->createElement('p');
$p1->appendChild($field);

$textarea = $form->createElement('textarea');
$textarea->setAttribute('cols', '50');
$textarea->setAttribute('rows', '5');
$textarea->setAttribute('name', 'comments');
$blank = $form->createTextNode('');
$textarea->appendChild($blank);
$p2 = $form->createElement('p');
$p2->appendChild($textarea);

$submit = $form->createElement('input');
$submit->setAttribute('type', 'submit');
$submit->setAttribute('name', 'submit');
$submit->setAttribute('value', 'Submit');
$p3 = $form->createElement('p');
$p3->appendChild($submit);

$formhead = $form->createElement('form');
$formhead->setAttribute('method', 'post');
$formhead->setAttribute('action', '');
$formhead->appendChild($p1);
$formhead->appendChild($p2);
$formhead->appendChild($p3);
$form->appendChild($formhead);
$form->formatOutput = true;
$out = $form->saveXML();
$out = explode("\n", $out);

// Remove empty array key at the end created from explode "\n"
array_pop($out);
// Remove the <?xml ... tag on the first line of the output.
array_shift($out);
// Put the string back together and add the "\n" back to each line.
$str = implode("\n", $out);
echo $str;
highlight_file(__FILE__);
?>
