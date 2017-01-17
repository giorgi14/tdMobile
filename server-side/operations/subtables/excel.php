<?php
require_once('../../../includes/classes/core.php');

$local_id	= $_REQUEST['local_id'];


$res =mysql_query("SELECT    client.id,
                             DATE_FORMAT(client.datetme,'%m') AS `month_id`,
                		     DATE_FORMAT(client.datetme,'%Y') AS `year`,
                             DATE_FORMAT(client.datetme,'%d') AS `day`,
            			     CONCAT(client.`name`, ' ', client.lastname) AS `name`,
                             client_loan_agreement.loan_amount,
                             client_loan_agreement.loan_months,
                             client_loan_agreement.percent,
                             client_loan_agreement.loan_type_id
                   FROM     `client`
                   JOIN     `month` ON `month`.id = DATE_FORMAT(client.datetme,'%m')
                   LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = client.id
                   WHERE     client.id = '$local_id'");
 	

$row    = mysql_fetch_assoc($res);
$number = $row[loan_months] +20;

$sum_percent = 0;
$sum_P       = 0;

$loan_type   = $row[loan_type_id];

$PV          = $row[loan_amount]; //სესხის მოცულობა
$r           = $row[percent]/100; //პროცენტი თვეში
$n           = $row[loan_months]; //სესხის ვადა თვეში
$year_month  = $row[percent]*12;
$hint        = 'წლ';

if ($loan_type == 1) {
    $P          = $PV*$r;
    $ziri       = 0.00;
    $percent    = $P;
    $year_month = $res[percent];
    $hint        = 'თვ';
    $sum_percent = $n*$percent;
    $sum_P       = $sum_percent+$PV;
}else {
    $P = ($PV*$r)/(1-(pow((1+$r),-$n))); //ყოველთვიური გადასახდელი
}
for ($i = 1; $i<=$n; $i++){
    $month        = $row[month_id]+$i;
    if ($loan_type == 1 && $i == $n) {
        $P       = $P + $row[loan_amount];
        $ziri    = $row[loan_amount];
        $PV      = 0.00;
        
    }elseif ($loan_type != 1){
        $percent      = $PV / $n * $r * $n; //ყოველთვიური გადასახდელი პროცენტი
        $ziri         = $P - $percent; //ყოველთვიური გადასახდელი ძირი
        $PV           = $PV - $ziri; //დარჩენილი ძირი
        $sum_percent += $percent;
        $sum_P        = $sum_P +$P;
    }

    if ($month<=12) {
        if ($month<10) {
            $month = '0'.$month;
        }
        $date = $row[day].'-'.$month.'-'.$row[year];
    }else{
        $month = $month - 12;
        if ($month<10) {
            $month = '0'.$month;
        }
        $year  = $row[year] +1;
        $date = $row[day].'-'.$month.'-'.$year;
    } 
    
	$dat .= '
			<ss:Row>
				<ss:Cell>
					<ss:Data ss:Type="String">'.$i.'</ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String">'.$date.'</ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String">'.round($ziri,2).'</ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String">'.round($percent,2).'</ss:Data>
				</ss:Cell>							
				<ss:Cell>
					<ss:Data ss:Type="String">'.round($P,2).'</ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String">'.round($PV,2).'</ss:Data>
				</ss:Cell>
			</ss:Row>';
}
$dat .= '   <ss:Row>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
                <ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
			</ss:Row>
			<ss:Row>
				<ss:Cell>
					<ss:Data ss:Type="String">ხელმოწერა: ლ</ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String">ხელმოწერა: ლ</ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
				<ss:Cell>
					<ss:Data ss:Type="String"></ss:Data>
				</ss:Cell>
			</ss:Row>';
	$name = "სესხის დაფარვის გრაფიკი";



$data = '
<?xml version="1.0" encoding="utf-8"?><?mso-application progid="Excel.Sheet"?>
<ss:Workbook xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:o="urn:schemas-microsoft-com:office:office">
	<o:DocumentProperties>
		<o:Title>'.$name.'</o:Title>
	</o:DocumentProperties>
	<ss:ExcelWorkbook>
		<ss:WindowHeight>9000</ss:WindowHeight>
		<ss:WindowWidth>50000</ss:WindowWidth>
		<ss:ProtectStructure>false</ss:ProtectStructure>
		<ss:ProtectWindows>false</ss:ProtectWindows>
	</ss:ExcelWorkbook>
	<ss:Styles>
		<ss:Style ss:ID="Default">
			<ss:Alignment ss:Vertical="Center" ss:Horizontal="Center" ss:WrapText="1" />
			<ss:Font ss:FontName="Sylfaen" ss:Size="12" />
			<ss:Interior />
			<ss:NumberFormat />
			<ss:Protection />
			<ss:Borders>
				<ss:Border ss:Position="Top" ss:Color="#000000" ss:Weight="1" ss:LineStyle="Continuous" />
				<ss:Border ss:Position="Bottom" ss:Color="#000000" ss:Weight="1" ss:LineStyle="Continuous" />
				<ss:Border ss:Position="Left" ss:Color="#000000" ss:Weight="1" ss:LineStyle="Continuous" />
				<ss:Border ss:Position="Right" ss:Color="#000000" ss:Weight="1" ss:LineStyle="Continuous" />
			</ss:Borders>
		</ss:Style>
		<ss:Style ss:ID="title">
			<ss:Borders />
			<ss:NumberFormat ss:Format="@" />
			<ss:Alignment ss:WrapText="1" ss:Horizontal="Center" ss:Vertical="Center" />
		</ss:Style>
		<ss:Style ss:ID="headercell">
			<ss:Font ss:Bold="1" />
			<ss:Interior ss:Pattern="Solid" />
			<ss:Alignment ss:WrapText="1" ss:Horizontal="Center" ss:Vertical="Center" />
		</ss:Style>
		<ss:Style ss:ID="headercell1">
			<ss:Font ss:Bold="1" />
			<ss:Interior ss:Pattern="Solid" />
			<ss:Alignment ss:WrapText="1" ss:Horizontal="Center" ss:Vertical="Center" />
		</ss:Style>
	</ss:Styles>
	<ss:Worksheet ss:Name="'.$number.'">
		<ss:Names>
			<ss:NamedRange ss:Name="Print_Titles" ss:RefersTo="=\' '.$number.' \'!R1:R2" />
		</ss:Names>
		
		<ss:Table x:FullRows="1" x:FullColumns="1" ss:ExpandedColumnCount="16" ss:ExpandedRowCount="'.$number.'">
		    
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Column ss:AutoFitWidth="1" ss:Width="100" />
			<ss:Row ss:Height="30">
				<ss:Cell ss:StyleID="title" ss:MergeAcross="15">
					<ss:Data xmlns:html="http://www.w3.org/TR/REC-html40" ss:Type="String">
						<html:B>
							<html:Font html:Size="14">'.$name.'</html:Font>
						</html:B>
					</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">კლიენტის სახელი:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
				<ss:Cell ss:MergeAcross="4" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row[name].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
			</ss:Row>
					    
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">სესხის მოცულობა:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row[loan_amount].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
				<ss:Cell ss:MergeAcross="1"  ss:StyleID="headercell1">
					<ss:Data ss:Type="String">სესხის გაცემის თარიღი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">საპროცენტო სარგ. ('.$hint.'.):</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$year_month.'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">თვე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			    <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row[month_id].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ვადა:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$n.'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">რიცხვი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			    <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row[day].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">საშეღავათო პერიოდი:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">წელი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			    <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row[year].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">საკომისიო წინასწარ:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			    <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell  ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">სულ პროცენტი:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">სულ დასაფარი:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.round($sum_percent,2).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.round($sum_P,2).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			    <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">0</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
			
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">#</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">თარიღი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ძირი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">პროცენტი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>				
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">შენატანი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ნაშთი შენატანის შემდეგ</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		
'; 
$data .= $dat; 
  
		
$data .='</ss:Table>
		<x:WorksheetOptions>
			<x:PageSetup>
				<x:Layout x:CenterHorizontal="1" x:Orientation="Portrait" />
				<x:Header x:Data="&amp;R&#10;&#10;&amp;D" />
				<x:Footer x:Data="Page &amp;P of &amp;N" x:Margin="0.5" />
				<x:PageMargins x:Top="0.5" x:Right="0.5" x:Left="0.5" x:Bottom="0.8" />
			</x:PageSetup>
			<x:FitToPage />
			<x:Print>
				<x:PrintErrors>Blank</x:PrintErrors>
				<x:FitWidth>1</x:FitWidth>
				<x:FitHeight>32767</x:FitHeight>
				<x:ValidPrinterInfo />
				<x:VerticalResolution>1000</x:VerticalResolution>
			</x:Print>
			<x:Selected />
			<x:DoNotDisplayGridlines />
			<x:ProtectObjects>False</x:ProtectObjects>
			<x:ProtectScenarios>False</x:ProtectScenarios>
		</x:WorksheetOptions>
	</ss:Worksheet>
</ss:Workbook>
		';

if($number == '2'){
	$null= 1;
	echo json_encode($null);
}else{
	echo json_encode($data);
}
file_put_contents('excel.xls', $data);



	