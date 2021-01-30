<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use Yajra\DataTables\Facades\DataTables;
use Hash;
use Auth;


class PrescriptionRepository
{

	public function getDatatable($request)
	{
		
		$from = $request->from;
		$to = $request->to;
		$prescriptions = Prescription::whereBetween('date', [$from, $to])->orderBy('code', 'asc')->get();

		return Datatables::of($prescriptions)
			->editColumn('code', function ($prescription) {
				return str_pad($prescription->code, 6, "0", STR_PAD_LEFT);
			})
			->addColumn('sub_total', function ($prescription) {
				return number_format($prescription->prescription_detail_sub_total(), 2);
			})
			->addColumn('discount', function ($prescription) {
				return number_format($prescription->prescription_discount_total(), 2);
			})
			->addColumn('grand_total', function ($prescription) {
				return number_format($prescription->prescription_detail_grand_total(), 2);
			})
			->addColumn('actions', function () {
				$button = '';
				return $button;
			})
			->make(true);
	}

	public function get_edit_detail($id)
	{
		$inv_detail = PrescriptionDetail::find($id);
		$medicine = $inv_detail->medicine;
		return $inv_detail;
	}

	public function getPrescriptionPreview($id)
	{

		$no = 1;
		$total = 0;
		$prescription_detail = '';
		$tbody = '';

		$prescription = Prescription::find($id);

		$title = 'Prescription (PRE-' . str_pad($prescription->code, 6, "0", STR_PAD_LEFT) . ')';

		foreach ($prescription->prescription_details as $prescription_detail) {
			$total = $prescription_detail->morning + $prescription_detail->afternoon + $prescription_detail->evening + $prescription_detail->night;
			$tbody .= '<tr>
									<td class="text-center">' . $no++ . '</td>
									<td>' . $prescription_detail->medicine_name . '</td>
									<td class="text-center">' . $prescription_detail->morning . '</td>
									<td class="text-center">' . $prescription_detail->afternoon . '</td>
									<td class="text-center">' . $prescription_detail->evening . '</td>
									<td class="text-center">' . $prescription_detail->night . '</td>
									<td class="text-center">' . $total . '</td>
									<td class="text-center">' . $prescription_detail->medicine_usage . '</td>
									<td><small>' . $prescription_detail->description . '</small></td>
								</tr>';
		}

		$prescription_detail = '<section class="prescription-print">
												<table class="table-header" width="100%">
													<tr>
														<td rowspan="5" width="20%" style="padding: 10px;">
															<img src="/images/setting/Logo.png" alt="IMG">
														</td>
														<td class="text-center" style="padding: 5px 0;">
															<h3 class="color_blue KHOSMoulLight" style="color: blue;">'. Auth::user()->setting()->clinic_name_kh .'</h3>
														</td>
													</tr>
													<tr>
														<td class="text-center" style="padding: 2px 0;">
															<h3 class="color_red roboto_b" style="color: red;">'. Auth::user()->setting()->clinic_name_en .'</h3>
														</td>
													</tr>
													<tr>
														<td class="text-center" style="padding: 1px 0;">
															<div>'. Auth::user()->setting()->description .'</div>
														</td>
													</tr>
													<tr>
														<td class="text-center" style="padding: 1px 0;">
															<div>អាសយដ្ឋាន: '. Auth::user()->setting()->address .'</div>
														</td>
													</tr>
													<tr>
														<td class="text-center" style="padding-bottom: 5px;">
															<div>លេខទូរស័ព្ទ: '. Auth::user()->setting()->phone .'</div>
														</td>
													</tr>
												</table>
												<table class="table-information" width="100%" style="border-top: 4px solid red; border-bottom: 4px solid red; margin: 10px 0;">
													<tr>
														<td colspan="3">
															<h5 class="text-center KHOSMoulLight" style="padding-top: 8px;">វិក្កយបត្រ</h5>
														</td>
													</tr>
													<tr>
														<td>
															កាលបរិច្ឆេទ/Date:<span class="date">'. date('d/m/Y', strtotime($prescription->date)) .'</span>
														</td>
														<td width="29%">
															Patient ID:<span class="pt_no">PT-'. str_pad($prescription->code, 6, "0", STR_PAD_LEFT) .'</span>
														</td>
														<td width="29%">
															No.:<span class="code">PRE'. str_pad($prescription->code, 6, "0", STR_PAD_LEFT) .'</span>
														</td>
													</tr>
													<tr>
														<td>
															ឈ្មោះ/Name:<span class="pt_name">'. $prescription->pt_name .'</span>
														</td>
														<td>
														អាយុ/Age:<span class="pt_age">'. $prescription->pt_age .'</span>
														</td>
														<td>
															ភេទ/Gender:<span class="pt_gender">'. $prescription->pt_gender .'</span>
														</td>
													</tr>
												</table>
												<table class="table-detail" width="100%">
													<thead>
														<th class="text-center" width="5%">ល.រ</th>
														<th class="text-center">ឈ្មោះថ្នាំ</th>
														<th class="text-center" width="6%">ព្រឹក</th>
														<th class="text-center" width="6%">ថ្ងៃ</th>
														<th class="text-center" width="6%">ល្ងាច</th>
														<th class="text-center" width="6%">យប់</th>
														<th class="text-center" width="6%">សរុប</th>
														<th class="text-center" width="13%">ការប្រើប្រាស់</th>
														<th class="text-center" width="19%">កំណត់ចំណាំ</th>
													</thead>
													<tbody>
														'. $tbody .'
													</tbody>
												</table>
												<table class="table-footer" style="margin-top: 15px;" width="100%">
													<tr>
														<td></td>
														<td width="32%" class="text-center">
															<div>រៀបចំដោយ/Prepared By</div>
															<div class="sign_box"></div>
															<div style="color: blue;">វេជ្ជបណ្ឌិត.<span class="color_blue KHOSMoulLight">'. Auth::user()->setting()->sign_name .'</span></div>
														</td>
													</tr>
												</table>
											</section>';

		return response()->json(['prescription_detail' => $prescription_detail, 'title' => $title]);
		// return $prescription_detail;

	}

	public function code()
	{
		$prescription = Prescription::whereYear('date', date('Y'))->orderBy('code', 'desc')->first();
		return (($prescription === null) ? '000001' : $prescription->code + 1);
	}

	public function create($request)
	{
		$prescription = Prescription::create([
			'date' => $request->date,
			'code' => $request->code,
			'pt_no' => $request->pt_no,
			'pt_age' => $request->pt_age,
			'pt_name' => $request->pt_name,
			'pt_gender' => $request->pt_gender,
			'pt_phone' => $request->pt_phone,
			'remark' => $request->remark,
			'patient_id' => $request->patient_id,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id,
		]);
		
		if (isset($request->medicine_id) && isset($request->medicine_name) && isset($request->medicine_usage)) {
			for ($i = 0; $i < count($request->medicine_id); $i++) {
				$prescription_detail = PrescriptionDetail::create([
						'medicine_name' => $request->medicine_name[$i],
						'medicine_usage' => $request->medicine_usage[$i],
						'morning' => $request->morning[$i],
						'afternoon' => $request->afternoon[$i],
						'evening' => $request->evening[$i],
						'night' => $request->night[$i],
						'description' => $request->description[$i],
						'index' => $i + 1,
						'medicine_id' => $request->medicine_id[$i],
						'prescription_id' => $prescription->id,
						'created_by' => Auth::user()->id,
						'updated_by' => Auth::user()->id,
					]);
			}
		}

		return $prescription;
	}

	// public function create_prescription_detail($request)
	// {

	// 	$prescription = Prescription::find($request->prescription_id);
	// 	$last_item = $prescription->prescription_details()->first();
	// 	$index = (($last_item !== null) ? $last_item->index + 1 : 1);
	// 	$medicine_id = explode(":;:", $request->medicine_id);

	// 	$prescription_detail = PrescriptionDetail::create([
	// 		'medicine_name' => $request->medicine_name,
	// 		'medicine_usage' => $request->medicine_usage,
	// 		'morning' => $request->morning,
	// 		'afternoon' => $request->afternoon,
	// 		'evening' => $request->evening,
	// 		'night' => $request->night,
	// 		'description' => $request->description,
	// 		'index' => $index,
	// 		'medicine_id' => $medicine_id[0],
	// 		'prescription_id' => $request->prescription_id,
	// 		'created_by' => Auth::user()->id,
	// 		'updated_by' => Auth::user()->id,
	// 	]);

	// 	return $prescription_detail;
	// }


	public function prescriptionDetailStore($request)
	{

		$prescription = Prescription::find($request->prescription_id);
		$last_item = $prescription->prescription_details()->first();
		$index = (($last_item !== null) ? $last_item->index + 1 : 1);

		$prescription_detail = PrescriptionDetail::create([
												'medicine_name' => $request->medicine_name,
												'medicine_usage' => $request->medicine_usage,
												'morning' => $request->morning,
												'afternoon' => $request->afternoon,
												'evening' => $request->evening,
												'night' => $request->night,
												'description' => $request->description,
												'index' => $index,
												'medicine_id' => $request->medicine_id,
												'prescription_id' => $request->prescription_id,
												'created_by' => Auth::user()->id,
												'updated_by' => Auth::user()->id,
											]);

		$json = $this->getPrescriptionPreview($prescription_detail->prescription_id)->getData();

		return response()->json([
			'success'=>'success',
			'prescription_detail' => $prescription_detail,
			'prescription_preview' => $json->prescription_detail,
		]);

	}
	public function prescriptionDetailUpdate($request)
	{
		$prescription_detail = PrescriptionDetail::find($request->id);
		$prescription_detail->update([
			'medicine_name' => $request->medicine_name,
			'medicine_usage' => $request->medicine_usage,
			'morning' => $request->morning,
			'afternoon' => $request->afternoon,
			'evening' => $request->evening,
			'night' => $request->night,
			'description' => $request->description,
			'medicine_id' => $request->medicine_id,
			'updated_by' => Auth::user()->id,
		]);

		$json = $this->getPrescriptionPreview($prescription_detail->prescription_id)->getData();
		return response()->json([
			'success'=>'success',
			'prescription_detail' => $prescription_detail,
			'prescription_preview' => $json->prescription_detail,
		]);
	}
	
	// public function update_prescription_detail($request)
	// {
	// 	$prescription = Prescription::find($request->edit_prescription_id);
	// 	$medicine_id = explode(":;:", $request->edit_medicine_id);
	// 	$prescription_detail = PrescriptionDetail::find($request->edit_id);
	// 	$prescription_detail->update([
	// 		'medicine_name' => $request->edit_medicine_name,
	// 		'medicine_usage' => $request->edit_medicine_usage,
	// 		'morning' => $request->edit_morning,
	// 		'afternoon' => $request->edit_afternoon,
	// 		'evening' => $request->edit_evening,
	// 		'night' => $request->edit_night,
	// 		'description' => $request->edit_description,
	// 		'medicine_id' => $medicine_id[0],
	// 		'updated_by' => Auth::user()->id,
	// 	]);
		
	// 	return $prescription_detail;
	// }

	public function save_order($request)
	{
		$order = explode(',', $request->order_ids);
		$ids = explode(',', $request->item_ids);

		for ($i = 0; $i < count($ids); $i++) {
			$prescription_detail = PrescriptionDetail::find($ids[$i])
				->update([
					'index' => $order[$i],
					'updated_by' => Auth::user()->id,
				]);
		}
		return 'success';
	}

	public function update($request, $prescription)
	{
		$prescription->update([
			'date' => $request->date,
			'code' => $request->code,
			'pt_no' => $request->pt_no,
			'pt_age' => $request->pt_age,
			'pt_name' => $request->pt_name,
			'pt_gender' => $request->pt_gender,
			'pt_phone' => $request->pt_phone,
			'remark' => $request->remark,
			'patient_id' => $request->patient_id,
			'updated_by' => Auth::user()->id,
		]);
		
		return $prescription;

	}

	public function status($prescription, $status)
	{
		$prescription->update([
			'status' => $status,
		]);

		return $prescription;
	}

	public function destroy($request, $prescription)
	{
		if (Hash::check($request->passwordDelete, Auth::user()->password)) {
			$code = $prescription->code;
			if ($prescription->delete()) {

				return $code;
			}
		} else {
			return false;
		}
	}

	public function destroy_prescription_detail($prescription_detail)
	{
		$code = $prescription_detail->prescription->code;
		if ($prescription_detail->delete()) {
				
			return $code;
		}
		

	}
}