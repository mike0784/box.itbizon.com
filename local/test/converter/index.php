<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Проверка тестового модуля");

require_once(__DIR__ . "/lib/convertio/autoload.php");
require_once(__DIR__ . "/lib/PHPWord/bootstrap.php");

use \PhpOffice\PhpWord\Settings;
use \Convertio\Convertio;
use \PhpOffice\PhpWord\Reader\Word2007;

Settings::loadConfig();

// Set writers
$writers = array('Word2007' => 'docx', 'ODText' => 'odt', 'RTF' => 'rtf', 'HTML' => 'html', 'PDF' => 'pdf');

// Turn output escaping on
Settings::setOutputEscapingEnabled(true);

if(isset($_FILES['doc'])) {
    $uploaddir = __DIR__ . "/";
    $uploadfile = $uploaddir . basename($_FILES['doc']['name']);

    move_uploaded_file($_FILES['doc']['tmp_name'], $uploadfile);
//    echo $uploadfile;

    $API = new Convertio("40cc4e467e592ad0cae26f93b6c10a9a");
//    $res = $API->start("./" . basename($_FILES['doc']['name']), 'docx',                 // Convert PDF (which contain scanned pages) into editable DOCX
//        [                                               // Setting Conversion Options (Docs: https://convertio.co/api/docs/#options)
//            'ocr_enabled' => true,                        // Enabling OCR
//            'ocr_settings' => [                           // Defining OCR Settings
//                'langs' => ['eng','rus'],                   // OCR language list (Full list: https://convertio.co/api/docs/#ocr_langs)
////                'page_nums' => '1-3,5,7'                    // Page numbers to process (optional)
//            ]
//        ]
//    )
//        ->wait()
//        ->download("./" . basename($_FILES['doc']['name']) . '.docx');                         // Download Result To Local File
//        ->delete();

//    $row = 1;
//    if (($handle = fopen(basename($_FILES['doc']['name']) . '.docx', "r")) !== FALSE) {
//        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//            $num = count($data);
//            echo "<p> $num полей в строке $row: <br /></p>\n";
//            $row++;
//            for ($c=0; $c < $num; $c++) {
//                echo $data[$c] . "<br />\n";
//            }
//        }
//        fclose($handle);
//    }

    $zip = new ZipArchive;
    $zip->open("./Trading.docx");
    $content = $zip->getFromName("word/document.xml");
    $xml = new DOMDocument();
    $xmlTables = new DOMDocument();
    $xmlTables->formatOutput = true;
    $xml->loadXML($content);
    $xmlTables->saveXML();

    $tables = $xml->getElementsByTagName("tbl");
    for ($i = 0; $i != $tables->length; $i++)
    {
        $node = $tables->item($i);
        $nodeNew = $xmlTables->importNode($node, true);
//        var_dump($nodeNew);
        $xmlTables->appendChild($nodeNew);
        break;
    }



    copy("./Trading.docx", "./Trading1.docx");

    $zipNew = new ZipArchive;
    $zipNew->open("./Trading1.docx");
    $zipNew->addFromString("word/document.xml",
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w10="urn:schemas-microsoft-com:office:word"><w:body>'.
        str_replace('<?xml version="1.0"?>', '', $xmlTables->saveXML())
            . '</w:body></w:document>'
    );
    $zipNew->close();

//    var_dump($xmlTables);

    echo "<table>";
    $doc = PhpOffice\PhpWord\IOFactory::load("./Trading1.docx");
    foreach ($doc->getSections() as $section) {

        foreach ($section->getElements() as $el) {


            if($el instanceof PhpOffice\PhpWord\Element\Table) {

                foreach ($el->getRows() as $indexRow => $row) {
                    echo "<tr>";
                    foreach ($row->getCells() as $col_index => $cell) {

                        echo "<td>";
                        foreach ($cell->getElements() as $cell_el) {
                            echo extract_text_from_element($cell_el);
                        }
                        echo "</td>";

                    }
                    echo "</tr>";
                }
            } else {
//                var_dump(extract_text_from_element($el));
            }
        }

    }
    echo "</table>";



}


function extract_text_from_element($el, $depth = 0)
{

    $c_text = "null";

    if ($depth > 100){
        echo "Depth of recursions is over the limit of 100 in";
        throw new \Exception("Depth of recursions is over the limit of 100 in " . __METHOD__);
    }

    if ($el instanceof PhpOffice\PhpWord\Element\Line) {
        $c_text = "\n\n";

    } else if ($el instanceof PhpOffice\PhpWord\Element\TextBreak) {
        $c_text = "\n";

    } else if ($el instanceof PhpOffice\PhpWord\Element\Text) {
        $c_text = $el->getText();

    } else if ($el instanceof PhpOffice\PhpWord\Element\TextRun) {
        $depth++;
        $a_elements = $el->getElements();

        $c_text = '';
        foreach($a_elements as $this_el) {
            $c_text .= extract_text_from_element($this_el, $depth);
        }//endforeach

        if (count($a_elements) > 0 ) {
            $c_text .= "\n";
        }//endif
    }//endif

    return $c_text;
}//end of function
//var_dump($_FILES['doc']);
//var_dump($_REQUEST);
?>

<!--    <iframe src="tmp.xml" frameborder="0"></iframe>-->

<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="doc" id="doc">
    <input type="submit" value="Отправить">
</form>


<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>