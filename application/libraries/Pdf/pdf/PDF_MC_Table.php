<?php
require('FPDF.php');

class PDF_MC_Table extends FPDF
{
var $widths;
var $aligns;

private $fontFamily = [];

private $fontStyle = [];

private $fontSize = [];

public $enableRowFonts = FALSE;

public $numRow = 1;

function SetWidths($w)
{

    
    //Set the array of column widths
    $this->widths=$w;
}





function setFontFamily( $fontFamily)
{
    $this->fontFamily = $fontFamily;
}

function setFontStyle( $fontStyle)
{
    $this->fontStyle = $fontStyle;
}


function setFontSize( $fontSize)
{
    $this->fontSize = $fontSize;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}



function FontBold( $fonts ){
    $this->fonts = $fonts;
}

function Row($data)
{
    $this->SetDrawColor(236,240,241);
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i] ,$data[$i]));
    $h=6*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    
    $last_y = 0;
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];

        if($this->enableRowFonts)
        {
            $family =isset($this->fontFamily[$i]) ? $this->fontFamily[$i] : 'Arial';
            $style  =isset($this->fontStyle[$i]) ? $this->fontStyle[$i] : '';
            $size   =isset($this->fontSize[$i]) ? $this->fontSize[$i] : 10;
            
            $this->SetFont( $family, $style, $size );
        }
        
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
       
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        $last_y = ($last_y<$this->GetY()) ? $this->GetY() : $last_y;
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }

   

    //Go to the next line
    $this->Ln($h);
    //$this->Rect(10,$last_y,196,0);
    $this->Line(10,$last_y, 206, $last_y);

    $this->numRow++;
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
}