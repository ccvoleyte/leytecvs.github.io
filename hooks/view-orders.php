<?php
	/* Assuming this custom file is placed inside 'hooks' */
	define('PREPEND_PATH', '../');
	$hooks_dir = dirname(__FILE__);
	include("{$hooks_dir}/../defaultLang.php");
	include("{$hooks_dir}/../language.php");
	include("{$hooks_dir}/../lib.php");
	
	include_once("{$hooks_dir}/../header.php");
	
	/* check access */
	$mi = getMemberInfo();
	// if(!in_array($mi['username'], array('john.doe', 'jane.doe'))){
	// if(!$mi['username'] || $mi['username'] == 'guest'){
	if(!in_array($mi['group'], array('Admins', 'Data entry'))){
		echo error_message("Access denied");
		include_once("{$hooks_dir}/../footer.php");
		exit;
	}
	
	/* VIEW SQL */
	$view_name = 'orders_info';
	$view_sql = "CREATE OR REPLACE VIEW '{$view_name}' AS
		SELECT
			o.OrderID, o.OrderDate, o.RequiredDate, o.ShippedDate,
			c.CompanyName AS 'Customer',
			c.City,
			c.Country,
			CONCAT_WS(' ', e.FirstName, e.LastName) AS 'Employee', 
			o.ShipVia AS 'Shipper', 
			SUM(d.UnitPrice * d.Quantity) AS 'Subtotal',
			o.Freight,
			SUM(d.UnitPrice * d.Quantity) + o.Freight  AS 'Total'
		FROM
			orders o LEFT JOIN
			order_details d ON o.OrderID=d.OrderID LEFT JOIN
			customers c ON c.CustomerID=o.CustomerID LEFT JOIN
			employees e ON e.EmployeeID=o.EmployeeID
		GROUP BY o.OrderID
	";
	
	sql($view_sql, $eo);
?>


	
<?php include_once("{$hooks_dir}/../footer.php"); ?>