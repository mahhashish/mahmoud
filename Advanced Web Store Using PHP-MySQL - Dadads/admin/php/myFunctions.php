<?php
	function disableComponents(){
		global $txtFnameDisable;	global $txtMnameDisable;	global $txtLnameDisable;	global $txtNoStreetDisable;
		global $txtCityDisable;	global $txtContactNoDisable;	global $txtDOBDisable;	global $txtAgeDisable;
		global $txtGenderDisable;	global $btnUpdateDisable;	global $btnDeleteDisable;
		$txtFnameDisable = "readonly";	$txtMnameDisable = "readonly";	$txtLnameDisable = "readonly";
		$txtNoStreetDisable = "readonly";	$txtCityDisable = "readonly";	$txtContactNoDisable = "readonly";
		$txtDOBDisable = "readonly";	$txtAgeDisable = "readonly";	$txtGenderDisable = "disabled";
		$btnUpdateDisable = "disabled"; $btnDeleteDisable = "disabled";
	}
	//----------------------------------------------------------------------------------------------------------
	function enableComponents(){
		global $txtFnameDisable;	global $txtMnameDisable;	global $txtLnameDisable;	global $txtNoStreetDisable;
		global $txtCityDisable;	global $txtContactNoDisable;	global $txtDOBDisable;	global $txtAgeDisable;
		global $txtGenderDisable;	global $btnUpdateDisable;	global $btnDeleteDisable;
		$txtFnameDisable = "";	$txtMnameDisable = "";	$txtLnameDisable = "";
		$txtNoStreetDisable = "";	$txtCityDisable = "";	$txtContactNoDisable = "";
		$txtDOBDisable = "";	$txtAgeDisable = "";	$txtGenderDisable = "";
		$btnUpdateDisable = ""; $btnDeleteDisable = "";
	}
	//----------------------------------------------------------------------------------------------------------
	function enableProductComponents(){
		global $imageFile; global $txtProdName; global $txtProdDescr; global $txtProdCat; global $txtProdPrice; global $txtProdQuan; global $btnUpdateDisable; global $btnDeleteDisable;
		$imageFile = ""; $txtProdName = ""; $txtProdDescr = ""; $txtProdCat = ""; $txtProdPrice = ""; $txtProdQuan = ""; $btnUpdateDisable = ""; $btnDeleteDisable = "";
	}
	//----------------------------------------------------------------------------------------------------------
	function disableProductComponents(){
		global $imageFile; global $txtProdName; global $txtProdDescr; global $txtProdCat; global $txtProdPrice; global $txtProdQuan; global $btnUpdateDisable; global $btnDeleteDisable;
		$imageFile = "disabled"; $txtProdName = "readonly"; $txtProdDescr = "readonly"; $txtProdCat = "disabled"; $txtProdPrice = "readonly";
		$txtProdQuan = "readonly"; $btnUpdateDisable = "disabled"; $btnDeleteDisable = "disabled";
	}
	//----------------------------------------------------------------------------------------------------------
	function selectUsersUsingId($uID){
		global $msg;
		global $userID; global $fname; global $mname; global $lname; global $noSt; global $city;
		global $contactNo; global $dob; global $age; global $gender; global $selectMaleGender; global $selectFemaleGender;
		$sqlSelUsers = mysql_query("select * from tblusers where user_id = '$uID'") or die(mysql_error());
		if(mysql_num_rows($sqlSelUsers) >= 1){
			$getRowUser = mysql_fetch_array($sqlSelUsers);
			$userID = $getRowUser["user_id"];
			$fname = $getRowUser["fname"];
			$mname = $getRowUser["mname"];
			$lname = $getRowUser["lname"];
			$noSt = $getRowUser["no_street"];
			$city = $getRowUser["city"];
			$contactNo = $getRowUser["contact_no"];
			$dob = $getRowUser["dob"];
			$age = $getRowUser["age"];
			$gender = $getRowUser["gender"];
			
			if($gender == "Male")
				$selectMaleGender = "selected";
			else if($gender == "Female")
				$selectFemaleGender = "selected";
			enableComponents();
		}else{
			$msg = "No record found!";
			disableComponents();
		}
	}
	//----------------------------------------------------------------------------------------------------------
	function selectProductUsingId($uID){
		global $msg;
		global $prodNo; global $prodID; global $prodName; global $prodDescr; global $prodCat; global $prodPrice; global $prodQuan;
		$sqlExistProd = mysql_query("select * from tblproduct where prod_id = '$uID'") or die(mysql_error());
		if(mysql_num_rows($sqlExistProd) >= 1){
			$getRowProd = mysql_fetch_array($sqlExistProd);
			$prodNo = $getRowProd["prod_no"];
			$prodID = $getRowProd["prod_id"];
			$prodName = $getRowProd["prod_name"];
			$prodDescr = $getRowProd["prod_descr"];
			$prodCat = $getRowProd["prod_cat"];
			$prodPrice = $getRowProd["prod_price"];
			$prodQuan = $getRowProd["prod_quan"];
			enableProductComponents();
		}else{
			$msg = "No record found";
			disableProductComponents();
		}
	}
?>