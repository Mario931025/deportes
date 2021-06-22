<?php

namespace App\Services\Pdf;

use Interpid\PdfLib\Pdf;
use Interpid\PdfLib\Multicell;
use Interpid\PdfLib\Table;

class ReportPdf extends Pdf
{
    public $title;
    
    public $description;
    
    public function Header()
    {
        $this->SetY(10);

        $multicell = Multicell::getInstance($this);

        $multicell->setStyle('p', 8, '', '160,160,160', 'arial');
        $multicell->setStyle('h1', 13, '', '160,160,160', 'arial');

        $multicell->multiCell(0, 5, "<h1>". $this->title ."</h1>", 0, 'C');
        $multicell->multiCell(0, 5, "<p>". $this->description ."</p>", 0, 'C');

        $this->SetY($this->tMargin);
    }
    
    public function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('arial', 'I', 7);
        $this->SetTextColor(170, 170, 170);
        $this->MultiCell(0, 4, "Página {$this->PageNo()} / {nb}", 0, 'C');
    }    
}