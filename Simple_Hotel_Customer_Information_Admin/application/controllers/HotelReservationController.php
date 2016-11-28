<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HotelReservationController extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('HotelreservationModel');
	}
	public function index()
	{
		$this->load->view('home');
	}
	public function view_all()
	{
		$customerDetails = $this->HotelreservationModel->view_all();

		echo "<center><p style='font-size: 15pt;'><b>Retrieved Information Of All Customer</b></p><hr/><br>
			<table>
				<tr>					
					<td style='font-size:12pt;font-weight:bold;'>Customer ID</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Firstname</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Middle Initial</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Lastname</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Email</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Contact Number</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Action</td>
				</tr>";
				
				foreach ($customerDetails as $value) 
				{
					echo "<tr onmouseover='ChangeColor(this, true);' onmouseout='ChangeColor(this, false);' 
				          >";//onclick='DoNav('http://www.yahoo.com/');'
						echo "<td>" . $value->cid . "</td>";
						echo "<td>" . $value->firstname . "</td>";
						echo "<td>" . $value->mi . "</td>";
						echo "<td>" . $value->lastname . "</td>";
						echo "<td>" . $value->Email . "</td>";
						echo "<td>" . $value->Phone . "</td>";
						echo "<td><a href='".site_url('HotelReservationController/delete')."/$value->cid'>Delete</a></td>";
					echo "</tr>";
				}
		  	echo "</center></table>";		
	}
	public function search()
	{
		$result = $this->input->post('search');
		$data = $this->HotelreservationModel->search($result);

		echo "<center><p style='font-size: 15pt;'><b>Search Result/s</b></p><hr/><br>
			<table>
				<tr>					
					<td style='font-size:12pt;font-weight:bold;'>Customer ID</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Firstname</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Middle Initial</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Lastname</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Email</td>
		  			<td style='font-size:12pt;font-weight:bold;'>Contact Number</td>
				</tr>";
				
				foreach ($data as $val) 
				{
					echo "<tr onmouseover='ChangeColor(this, true);' onmouseout='ChangeColor(this, false);' 
				          >";//onclick='DoNav('http://www.yahoo.com/');'
						echo "<td>" . $val->cid . "</td>";
						echo "<td>" . $val->firstname . "</td>";
						echo "<td>" . $val->mi . "</td>";
						echo "<td>" . $val->lastname . "</td>";
						echo "<td>" . $val->Email . "</td>";
						echo "<td>" . $val->Phone . "</td>";
						echo "<td><a href='".site_url('HotelReservationController/delete')."/$val->cid'>Delete</a></td>";
					echo "</tr>";
				}
		  	echo "</center></table>";		
	}
	public function registration()
	{
		$this->HotelreservationModel->add();
	}

	public function update()
	{
		$cid = $this->input->post('cid');
		$this->HotelreservationModel->update($cid);
	}

	public function delete($cid)
	{
		$this->db->where('cid',$cid);
		$this->db->delete('customer');
		redirect('HotelReservationController/index');
	}
}
