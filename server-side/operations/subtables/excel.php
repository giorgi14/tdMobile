<?php
require_once('../../../includes/classes/core.php');

$local_id	= $_REQUEST['local_id'];
$file_type	= $_REQUEST['file_type'];

if ($file_type == 'payment_schedule') {
    $res =mysql_query("SELECT    client.id,
            			     CONCAT(client.`name`, ' ', client.lastname) AS `name`,
                             DATE_FORMAT(client_loan_agreement.datetime,'%m') AS `month_id`,
                    		 DATE_FORMAT(client_loan_agreement.datetime,'%Y') AS `year`,
                             DATE_FORMAT(client_loan_agreement.datetime,'%d') AS `day`,
                             client_loan_agreement.loan_amount,
                             client_loan_agreement.loan_months,
                             client_loan_agreement.percent,
                             client_loan_agreement.loan_type_id
                   FROM     `client`
                   LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = client.id
                   LEFT JOIN `month` ON `month`.id = DATE_FORMAT(client_loan_agreement.datetime,'%m')
                   WHERE     client.id = '$local_id' AND client.actived=1 LIMIT 1");
 	

    $row1    = mysql_fetch_assoc($res);

    $sum_percent   = 0;
    $sum_P         = 0;
    
    $loan_agreement_type   = $row1[loan_type_id];
    $loan_amount           = $row1[loan_amount];
    $n                     = $row1[loan_months];
    $month_id              = $row1[month_id];
    $day                   = $row1[day];
    $year_start            = $row1[year];
    $name                  = $row1[name];
    $loan_type             = $loan_agreement_type;
            
    if ($loan_type == 2){
        $year_month    = $row1[percent]*12;
        $hint          = 'წლ';
    }else{
        $hint = 'თვ';
        $year_month    = $row1[percent];
    }
            
    $req = mysql_query("SELECT client_loan_schedule.number,
                    			client_loan_schedule.schedule_date,
                    			client_loan_schedule.root,
                    			client_loan_schedule.percent,
                    			client_loan_schedule.pay_amount,
                    			client_loan_schedule.remaining_root
                         FROM   client_loan_schedule
                         JOIN   client_loan_agreement ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
                         WHERE  client_loan_agreement.client_id = $local_id AND client_loan_schedule.actived=1");
    
    $number = mysql_num_rows($req)+25;        
    while ($row = mysql_fetch_assoc($req)){
        $sum_percent += $row[percent];
        $sum_P       += $row[pay_amount];
        
        $dat .= '<ss:Row>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[number].'</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[schedule_date].'</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[root].'</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[percent].'</ss:Data>
    				</ss:Cell>							
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[pay_amount].'</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[remaining_root].'</ss:Data>
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
					<ss:Data ss:Type="String">'.$row1[name].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
			</ss:Row>
					    
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">სესხის მოცულობა:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles" />
				</ss:Cell>
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1[loan_amount].'</ss:Data>
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
					<ss:Data ss:Type="String">'.$row1[month_id].'</ss:Data>
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
					<ss:Data ss:Type="String">'.$row1[day].'</ss:Data>
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
					<ss:Data ss:Type="String">'.$row1[year].'</ss:Data>
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

}elseif ($file_type == 'download_insurance'){
    $res =mysql_query("SELECT    client.id,
        CONCAT(client.`name`, ' ', client.lastname) AS `name`,
        DATE_FORMAT(client_loan_agreement.datetime,'%m') AS `month_id`,
        DATE_FORMAT(client_loan_agreement.datetime,'%Y') AS `year`,
        DATE_FORMAT(client_loan_agreement.datetime,'%d') AS `day`,
        client_loan_agreement.loan_amount,
        client_loan_agreement.loan_months,
        client_loan_agreement.percent,
        client_loan_agreement.loan_type_id
        FROM     `client`
        LEFT JOIN client_loan_agreement ON client_loan_agreement.client_id = client.id
        LEFT JOIN `month` ON `month`.id = DATE_FORMAT(client_loan_agreement.datetime,'%m')
        WHERE     client.id = '$local_id' AND client.actived=1 LIMIT 1");
    
    
    $row1    = mysql_fetch_assoc($res);
    
    $sum_percent   = 0;
    $sum_P         = 0;
    
    $loan_agreement_type   = $row1[loan_type_id];
    $loan_amount           = $row1[loan_amount];
    $n                     = $row1[loan_months];
    $month_id              = $row1[month_id];
    $day                   = $row1[day];
    $year_start            = $row1[year];
    $name                  = $row1[name];
    $loan_type             = $loan_agreement_type;
    
    if ($loan_type == 2){
        $year_month    = $row1[percent]*12;
        $hint          = 'წლ';
    }else{
        $hint = 'თვ';
        $year_month    = $row1[percent];
    }
    
    $req = mysql_query("SELECT client_loan_schedule.number,
        client_loan_schedule.schedule_date,
        client_loan_schedule.root,
        client_loan_schedule.percent,
        client_loan_schedule.pay_amount,
        client_loan_schedule.remaining_root
        FROM   client_loan_schedule
        JOIN   client_loan_agreement ON client_loan_schedule.client_loan_agreement_id = client_loan_agreement.id
        WHERE  client_loan_agreement.client_id = $local_id AND client_loan_schedule.actived=1");
    
    $number = 100;
    while ($row = mysql_fetch_assoc($req)){
        $sum_percent += $row[percent];
        $sum_P       += $row[pay_amount];
    
        $dat .= '<ss:Row>
    				<ss:Cell>
    					<ss:Data ss:Type="String">345345</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">345</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">345</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">345</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">345</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">345345</ss:Data>
    				</ss:Cell>
    			</ss:Row>
    			';
    }
    
    $name = "დაზღვევა ალდაგი";
    
    
    
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
		    
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">სურათი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">პიროვნებათა მონაცემები, რომლებიც მართავენ ავტოტრანსპორტს</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ინფორმაცია ავტოტრანსპორტის შესახებ</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">№</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის მარკა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ინფორმაცია ავტოტრანსპორტის შესახებ</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ძრავის ტიპი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ადგილების რაოდენობა მძღოლის ჩათვლით</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ინფორმაცია ავტოტრანსპორტის შესახებ</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ძრავის მოცულობა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">1</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">№</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">გამოშვების თარიღი	</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრასნპორტის დღევანდელი ღირებულება</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის სარეგსიტრაციო ნომერი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">შეძენის თარიღი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">სესხის ოდენობა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">1</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    
		    
		    
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">თუ აღნიშნული ცხრილი არ არის საკმარისი, გთხოვთ დაურთოთ განაცხადს დანართი შესაბამისი ორგანიზაციის ბეჭდითა და ხელმოწერით</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">დაზღვეული ავტომობილის აღწერა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    
		    
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის დაზღვევის განაცხადი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    ';
     //$data .= $dat;
    
    
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
</ss:Workbook>';
}
echo json_encode($data);

file_put_contents('excel.xls', $data);



	