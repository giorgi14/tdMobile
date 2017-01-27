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
    
    function yes_no($id){
        
            $data .= '<option value="0" selected="selected">----</option>';
        
            if ($id == '') {
                $data .= '<option value="1">დიახ</option>';
                $data .= '<option value="2">არა</option>';
                $data1='';
            }else{
                if($id == 1){
                    $data .= '<option value="1" selected="selected">დიახ</option>';
                    $data .= '<option value="2">არა</option>';
                    
                    $data1='დიახ';
                } elseif ($id == 2) {
                    $data .= '<option value="2" selected="selected">არა</option>';
                    $data .= '<option value="1">დიახ</option>';
                    $data1='არა';
                }
            }
        
            return $data1;
        }
    
    $row1 =mysql_fetch_assoc(mysql_query("SELECT    CONCAT(client.name, ' ', client.lastname) AS name,
                                                    client.pid,
                                                    client.actual_address,
                                                    client.juridical_address,
                                                    client.phone,
                                                    client_loan_agreement.loan_amount,
                                                    client_car.car_marc,
                                                    client_car.car_wheel,
                                                    client_car.car_seats,
                                                    client_car.car_price,
                                                    client_car.car_sale_date,
                                                    client_car.car_insurance_price,
                                                    client_car.car_ins_start,
                                                    client_car.car_ins_end,
                                                    client_car.model,
                                                    client_car.car_id,
                                                    client_car.manufacturing_date,
                                                    client_car.color,
                                                    client_car.registration_number,
                                                    car_type.name AS car_type_name,
                                                    client_car.engine_size,
                                                    client_car.certificate_id,
                                                    car_insurance_info.datetime AS car_insurance_info_datetime,
                                                    car_insurance_info.id AS car_insurance_info_id,
                                                    car_insurance_info.lined_organization_yes_no,
                                                    car_insurance_info.any_person_Managed_yes_no,
                                                    car_insurance_info.encased_yes_no,
                                                    car_insurance_info.signaling_yes_no,
                                                    car_insurance_info.autotransport_other_protection_yes_no,
                                                    car_insurance_info.signaling_type,
                                                    car_insurance_info.driver_disabled_yes_no,
                                                    car_insurance_info.driver_no_ins_yes_no,
                                                    car_insurance_info.car_accident_drivers_yes_no,
                                                    car_insurance_info.guilt_drivers_yes_no,
                                                    car_insurance_info.injury_passion_ins_yes_no,
                                                    car_insurance_info.responsible_ins_limit,
                                                    car_insurance_info.driver_or_passenger_ins_limit,
                                                    car_insurance_info.public_private_yes_no,
                                                    car_insurance_info.trade_yes_no,
                                                    car_insurance_info.trade_yes_no1,
                                                    car_insurance_info.trade_yes_no2,
                                                    car_insurance_info.trade_yes_no3,
                                                    car_insurance_info.trade_yes_no4,
                                                    car_insurance_info.goods_or_ardware_yes_no,
                                                    car_insurance_info.Insured_yes_no,
                                                    car_insurance_info.insurance_company,
                                                    car_insurance_info.insurance_price_gel,
                                                    car_insurance_info.insurance_price_usd,
                                                    car_insurance_info.insurance_start_date,
                                                    car_insurance_info.insurance_end_date
                                         FROM       `client`
                                         LEFT JOIN  client_loan_agreement ON client_loan_agreement.client_id = client.id
                                         LEFT JOIN  client_car ON client_car.client_id = client.id
                                         LEFT JOIN  car_type ON car_type.id = client_car.type_id
                                         LEFT JOIN  car_insurance_info ON car_insurance_info.client_id = client.id
                                         WHERE      client.id = '$local_id'"));
    
    
    
    $req = mysql_query("SELECT `name`,
                				born_date,
                				driving_license_type,
                				driving_license_date
                        FROM   `client_car_drivers`
                        WHERE   actived = 1 AND client_id = '$local_id'");
    
    $number = 100;
    $i = 1;
    while ($row = mysql_fetch_assoc($req)){
        
        $dat .= '<ss:Row>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$i.'</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[name].'</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String"></ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String"></ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[born_date].'</ss:Data>
    				</ss:Cell>
    				<ss:Cell>
    					<ss:Data ss:Type="String">'.$row[driving_license_type].'</ss:Data>
    				</ss:Cell>
                    <ss:Cell ss:MergeAcross="1">
    					<ss:Data ss:Type="String">'.$row[driving_license_date].'</ss:Data>
    				</ss:Cell>
    			</ss:Row>
    			';
        $i+=1;
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
					<ss:Data ss:Type="String">დამზღვევის რეკვიზიტები</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">შ.პ.ს. თი ჯი მობაილ</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">მოსარგებლე (საზღაურის მიმღები)</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">შ.პ.ს. თი ჯი მობაილ</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">დამზღვევის იურიდიული მისამართი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">თბილისი, დოლიძის 6</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">დამზღვევის ფაქტობრივი მისამართი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">თბილისი, კერესელიძის 12</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">საიდენტიფიკაციო კოდი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">205270277</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">საქმიანობის ტიპი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოლომბარდი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ხელმძღვანელის თანამდებობა, სახელი გვარი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">დირ. გიორგი კილაძე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის მფლობელი (მიუთითეთ პირადი ნომერი)</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1[name].' (პირადი # '.$row1[pid].')</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ავტოტრანსპორტის მფლობელის მისამართი (მობილური)</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1[actual_address].' '.$row1[juridical_address].' '.$row1[phone].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">პიროვნებათა მონაცემები, რომლებიც მართავენ ავტოტრანსპორტს</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    '.$dat.'
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
					<ss:Data ss:Type="String">მოდელი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">რომელ მხარეს მდებარეობს საჭე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ძრავის ტიპი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ინფორმაცია ავტოტრანსპორტის შესახებ</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ძრავის მოცულობა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['car_marc'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['model'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['car_marc'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['car_wheel'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['car_type_name'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['car_seats'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['engine_size'].'</ss:Data>
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
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
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
					<ss:Data ss:Type="String">'.$row1['manufacturing_date'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['car_price'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['registration_number'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['car_sale_date'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['loan_amount'].'</ss:Data>
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
					<ss:Data ss:Type="String">გაფორმებულია თუ არა ავტოტრანსპორტი ორგანიზაციის სახელზე </ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['lined_organization_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">მართვას თუ არა ავტოტრანსპორტს ნებისმიერი პიროვნება 25 წლამდე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['any_person_Managed_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">მოთავსებულია თუ არა ავტოტრანსპორტი დაკეტილ ავტოფარეხში ან დაცულ ავტოსადგომზე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['encased_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">გააჩნია თუ არა ავტოტრანსპორტს სიგნალიზაცია</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['signaling_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">გააჩნია თუ არა ავტოტრანსპორტს სხვა დამცავი საშუალება</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['autotransport_other_protection_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">გთხოვთ მიუთითოთ სიგნალიზაციის ტიპი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">მონაცემები მართვის შესახებ და სარჩელები</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">არის თუ არა ზემოაღნიშნული რომელიმე მძღოლი ინვალიდი, უჩივის თუ არა მხედველობას, სმენას, ეპილეფსიას, დიაბეტს და/ან გულის დაავადებას</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['driver_disabled_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">აქვს თუ არა ზემოაღნიშნულ რომელიმე მძღოლს მიღებული უარი ნებისმიერი ავტომანქანის დაზღვევაზე, ან სადაზღვევო პერიოდის გაგრძელებაზე, ან საჭირო იყო თუ არა გაზრდილი სადაზღვევო გადასახადის გადახდა, ან მზღვეველის მიერ წაყენებული იყო თუ არა განსაკუთრებული პირობები</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['driver_no_ins_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">არის თუ არა ზემოაღნიშნული რომელიმე მძღოლი ნასამართლევი ავტოსაგზაო შემთხვევით ჩადენილ დანაშაულზე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['car_accident_drivers_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
			<ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">აქვს თუ არა ზემოაღნიშნულ რომელიმე მძღოლს ჩადენილი რაიმე დანაშაული ბოლო 3 წლის განმავლობაში</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['guilt_drivers_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">დაზღვევის სახეობა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ამოირჩიეთ თქვენთვის სასურველი დაზღვევის სახეობა:</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">დაზიანება, გატაცებ (ქურდობა,ძარცვა-ყაჩაღობა)</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['injury_passion_ins_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">თუ გსურთ მესამე მხარისადმი პასუხისმგებლობის დაზღვევა აირჩიეთ ლიმიტი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['responsible_ins_limit'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">თუ გსურთ უბედური შემთხვევით გამოწვეული მძღოლის და/ან მგზავრის დაზღვევა აირჩიეთ ლიმიტი მანქანაზე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['driver_or_passenger_ins_limit'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">რა მიზნებისთვის გამოიყენება ავტოტრანსპორტი?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">საზოგადოებრივი, პირადი და გასართობი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['public_private_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">მხოლოდ სამუშაო ადგილამდე მისვლა და უკან დაბრუნება</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['trade_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ორგანიზაციის საქმიანობასთან დაკავშირებით</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['trade_yes_no1']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">სხვადასხვა სამუშაო ადგილებზე გადასაადგილებლად</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['trade_yes_no2']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">სხვა პიროვნების მიერ ნებისმიერ საქმიანობასთან დაკავშირებით</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['trade_yes_no3']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ვაჭრობასთან ან ნებისმიერ სხვა საქმიანობასთან დაკავშირებით</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['trade_yes_no4']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">საქონლის ან აპარატურის გადასაადგილებლად</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['goods_or_ardware_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">მიმდინარე მდგომარეობა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="5" ss:StyleID="headercell">
					<ss:Data ss:Type="String">არის/ყოფილა თუ არა ორგანიზაციის ავტოტრანსპორტი დაზღვეული</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.yes_no($row1['Insured_yes_no']).'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">წინა კითხვაზე დადებითი პასუხის შემთხვევაში გთხოვთ მიუთითოთ დეტალები</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String">სადაზღვეო კომპანია "'.$row1['insurance_company'].'"</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="2" ss:StyleID="headercell">
					<ss:Data ss:Type="String">სადაზღვევო თანხა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['insurance_price_gel'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">ლარი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['insurance_price_usd'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">აშშ დოლარი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="2" ss:StyleID="headercell">
					<ss:Data ss:Type="String">დაზღვევის პერიოდი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['insurance_start_date'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">- დან</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1['insurance_end_date'].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:StyleID="headercell">
					<ss:Data ss:Type="String">- მდე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="7" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ჩემი ხელმოწერით ვადასტურებ, რომ აღნიშნულ განაცხადში მითითებული ინფორმაცია სრულია და ჭეშმარიტი და არასწორად მოწოდებული ინფორმაციის შემთხვევაში კომპანია უფლებამოსილია გააუქმოს პოლისი და უარი თქვას ზარალის ანაზღაურებაზე</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		    </ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">შევსების თარიღი</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">'.$row1[car_insurance_info_datetime].'</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">თანამდებობა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">?</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>
		    <ss:Row ss:AutoFitHeight="1" ss:Height="25">
				<ss:Cell ss:MergeAcross="3" ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String">ხელმოწერა</ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
		        <ss:Cell ss:MergeAcross="1" ss:StyleID="headercell">
					<ss:Data ss:Type="String"></ss:Data>
					<ss:NamedCell ss:Name="Print_Titles"/>
				</ss:Cell>
			</ss:Row>';
    
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



	