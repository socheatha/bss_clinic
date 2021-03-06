@extends('layouts.app')

@section('css')
	<style type="text/css">
		.table td{
			vertical-align: middle;
		}
	</style>
@endsection

@section('content')
<div class="card">
	<div class="card-header">
		<b>{!! Auth::user()->subModule() !!}</b>
		<a href="{{$labor_type != 1 ? route('labor.create') . '?labor_type=1' : '#' }}" class="btn btn-info btn-sm btn-flat {{ $labor_type == 1 ? 'active' : '' }}"><i class="fa fa-cubes"></i> &nbsp; ស្តង់ដា </a>
		<a href="{{$labor_type != 2 ? route('labor.create') . '?labor_type=2' : '#' }}" class="btn btn-info btn-sm btn-flat {{ $labor_type == 2 ? 'active' : '' }}"><i class="fa fa-cube"></i> &nbsp; ធម្មតា</a>
		<a href="{{$labor_type != 3 ? route('labor.create') . '?labor_type=3' : '#' }}" class="btn btn-info btn-sm btn-flat {{ $labor_type == 3 ? 'active' : '' }}"><i class="fa fa-file"></i> &nbsp; ទទេ</a>
		<div class="card-tools">
			@can('Labor Report')
			<a href="{{route('labor.report')}}" class="btn btn-info btn-sm btn-flat"><i class="fa fa-file-alt"></i> &nbsp;{{ __('label.buttons.report', [ 'name' => Auth::user()->module() ]) }}</a>
			@endcan
			@can('Labor Index')
			<a href="{{route('labor.index')}}" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-table"></i> &nbsp;{{ __('label.buttons.back_to_list', [ 'name' => Auth::user()->module() ]) }}</a>
			@endcan
		</div>

		<!-- Error Message -->
		@component('components.crud_alert')
		@endcomponent

	</div>

	{!! Form::open(['url' => route('labor.store'),'id' => 'submitForm','method' => 'post','class' => 'mt-3', 'autocomplete' => 'off']) !!}
	<div class="card-body">
		@include('labor.form')
		@if(in_array($labor_type, [1, 2]))
			<div class="card card-outline card-primary mt-4">
				<div class="card-header">
					<h3 class="card-title">
						<i class="fas fa-list"></i>&nbsp;
						{{ __('alert.modal.title.labor_detail') }}
					</h3>
					@if($labor_type == 1)
						<div class="card-tools">
							<button type="button" class="btn btn-flat btn-sm btn-success btn-prevent-submit" id="btn_add_service"><i class="fa fa-plus"></i> {!! __('label.buttons.add_item') !!}</button>
						</div>
					@endif
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					@if($labor_type == 1)
						<table class="table table-bordered" width="100%">
							<thead>
								<tr>
									<th width="60px">{!! __('module.table.no') !!}</th>
									<th>{!! __('module.table.name') !!}</th>
									<th width="200px">{!! __('module.table.labor.result') !!}</th>
									<th width="200px">{!! __('module.table.labor_service.unit') !!}</th>
									<th width="200px">{!! __('module.table.labor_service.reference') !!}</th>
									<th width="90px">{!! __('module.table.action') !!}</th>
								</tr>
							</thead>
							<tbody class="item_list">
							</tbody>
						</table>
					@elseif($labor_type == 2)
						<?php 
							$default_elements = '<h5 style="text-align:center"><u><strong>Main Title</strong></u></h5>

								<p>__checkbox__&nbsp;<u><strong>Sub-Title 1</strong></u></p>
								
								<table align="center" border="1" cellpadding="1" cellspacing="1" style="width:100%">
									<tbody>
										<tr>
											<td>__checkbox__ 11</td>
											<td>22</td>
											<td>33</td>
										</tr>
										<tr>
											<td>__checkbox__ 111</td>
											<td>222</td>
											<td>333</td>
										</tr>
									</tbody>
								</table>
								
								<p>&nbsp;</p>
								
								<p>__checkbox__&nbsp;<u><strong>Sub-Title 2</strong></u></p>
								
								<table align="center" border="1" cellpadding="1" cellspacing="1" style="width:100%">
									<tbody>
										<tr>
											<td>__checkbox__ aa</td>
											<td>bb</td>
											<td>cc</td>
										</tr>
										<tr>
											<td>__checkbox__ aaa</td>
											<td>bbb</td>
											<td>ccc</td>
										</tr>
										<tr>
											<td>__checkbox__ aaaa</td>
											<td>bbbb</td>
											<td>cccc</td>
										</tr>
									</tbody>
								</table>
								
								<p>&nbsp;</p>
							';
						?>
						<div class="form-group">
							{!! Html::decode(Form::label('description', __('label.form.description') .'<small>*</small>')) !!}
							{!! Form::textarea('simple_labor_detail', $default_elements, ['class' => 'form-control ','style' => 'height: 121px;', 'placeholder' => 'description', 'id' => 'my-editor', 'required']) !!}							
						</div>
					@endif
				</div>			
				<!-- /.card-body -->
			</div>
		@endif
	</div>
	<!-- ./card-body -->
	
	<div class="card-footer text-muted text-center">
		@include('components.submit')
	</div>
	<!-- ./card-Footer -->
	{{ Form::close() }}

</div>

@include('labor.modal')

@endsection

@section('js')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
	var editor = CKEDITOR.replace('my-editor', {
		height: '350',
		font_names: 'Calibrib Bold; Calibri Italic; Calibri; Roboto Regular; Roboto Bold; Khmer OS Battambang; Khmer OS Muol Light; Khmer OS Content; Khmer OS Kuolen;',
		toolbar: [
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'ExportPdf', 'Preview', 'Print', '-', 'Templates' ] },
			{ name: 'insert', items: ['Table' ] },
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ]},
		]
	});

	var endLoadScript = function () {} // declear global variable as function

	$('#btn_add_service').click(function () {
		$('#create_labor_item_modal').modal();
		$('#category_id').val('1').trigger('change');
	});

	$('#category_id').change(function () {
		if ($(this).val() != '') {
			$('#check_all_service').iCheck('uncheck');
			$.ajax({
				url: "{{ route('labor.getLaborServiceCheckList') }}",
				method: 'post',
				data: {
					id: $(this).val(),
				},
				success: function (data) {
					$('.service_check_list').html(data.service_check_list);
					
					$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
						checkboxClass: 'icheckbox_minimal-blue',
						radioClass   : 'iradio_minimal-blue'
					})
					$('#check_all_service').on('ifChecked', function (event) {
						$('.chb_service').iCheck('check');
						triggeredByChild = false;
					});
					$('#check_all_service').on('ifUnchecked', function (event) {
						if (!triggeredByChild) {
							$('.chb_service').iCheck('uncheck');
						}
						triggeredByChild = false;
					});
					// Removed the checked state from "All" if any checkbox is unchecked
					$('.chb_service').on('ifUnchecked', function (event) {
						triggeredByChild = true;
						$('#check_all_service').iCheck('uncheck');
					});
					$('.chb_service').on('ifChecked', function (event) {
						if ($('.chb_service').filter(':checked').length == $('.chb_service').length) {
							$('#check_all_service').iCheck('check');
						}
					});
				}
			});
		}else{
			$('.service_check_list').html('');
		}
	});
	
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_minimal-blue',
		radioClass   : 'iradio_minimal-blue'
	})
	$('#check_all_service').on('ifChecked', function (event) {
		$('.chb_service').iCheck('check');
		triggeredByChild = false;
	});
	$('#check_all_service').on('ifUnchecked', function (event) {
		if (!triggeredByChild) {
			$('.chb_service').iCheck('uncheck');
		}
		triggeredByChild = false;
	});
	// Removed the checked state from "All" if any checkbox is unchecked
	$('.chb_service').on('ifUnchecked', function (event) {
		triggeredByChild = true;
		$('#check_all_service').iCheck('uncheck');
	});
	$('.chb_service').on('ifChecked', function (event) {
		if ($('.chb_service').filter(':checked').length == $('.chb_service').length) {
			$('#check_all_service').iCheck('check');
		}
	});

	$('[name="pt_province_id"]').change( function(e){
		if ($(this).val() != '') {
			$.ajax({
				url: "{{ route('province.getSelectDistrict') }}",
				method: 'post',
				data: {
					id: $(this).val(),
				},
				success: function (data) {
					$('[name="pt_district_id"]').attr({"disabled":false});
					$('[name="pt_district_id"]').html(data);
					endLoadScript(); endLoadScript = function () {}; // execute this function then remove it
				}
			});
		}else{
			$('[name="pt_district_id"]').attr({"disabled":true});
			$('[name="pt_district_id"]').html('<option value="">{{ __("label.form.choose") }}</option>');
			
		}
	});

	$('.btn-prevent-submit').click(function (event) {
		event.preventDefault();
	});

	$('#btn_add_item').click(function (event) {
		event.preventDefault();

		var service_ids = [];
		var n = $( ".labor_item" ).length;
		$( ".chb_service" ).each(function( index ) {
			if ($(this).is(':checked')) {
				service_ids.push($(this).val());
			}
		});

		if (service_ids.length != 0) {
			$.ajax({
				url: "{{ route('labor.getCheckedServicesList') }}",
				method: 'post',
				data: {
					ids: service_ids,
					no: n,
				},
				success: function (data) {
					$('.item_list').append(data.checked_services_list);
					$('#check_all_service').iCheck('uncheck');
					$('#category_id').val('').trigger('change');
					$('#service_check_list').html('');
					$('#create_labor_item_modal').modal('hide');
					$(".is_number").keyup(function () {
						isNumber($(this))
					});
				}
			});
		}

	});

	function removeCheckedService(id) {
		$('#'+id).remove();
	}

	$(".select2_pagination").change(function () {
		$('[name="txt_search_field"]').val($('.select2-search__field').val());
	});
	
	function select2_search (term) {
		$(".select2_pagination").select2('open');
		var $search = $(".select2_pagination").data('select2').dropdown.$search || $(".select2_pagination").data('select2').selection.$search;
		$search.val(term);
		$search.trigger('keyup');
	}

	$(".select2_pagination").select2({
		theme: 'bootstrap4',
		placeholder: "{{ __('label.form.choose') }}",
		allowClear: true,
		ajax: {
			url: "{{ route('patient.getSelect2Items') }}",
			method: 'post',
			dataType: 'json',
			data: function(params) {
				return {
						term: params.term || '',
						page: params.page || 1
				}
			},
			cache: true
		}
	});	

	function load_service_info(id, _this){
		_this = $(_this);
		let value = _this.val();
		if($('option[value="'+value+'"]').data('price')) $('#input-price-'+id).val($('option[value="'+value+'"]').data('price'));
		if($('option[value="'+value+'"]').data('description')) $('#input-description-'+id).val($('option[value="'+value+'"]').data('description'));
	}

	$('#patient_id').change(function () {
		if ($(this).val()!='') {
			$.ajax({
				url: "{{ route('patient.getSelectDetail') }}",
				type: 'post',
				data: {
					id : $(this).val()
				},
			})
			.done(function( result ) {
				// $('[name="pt_no"]').val(result.patient.no);
				$('[name="pt_name"]').val(result.patient.name);
				$('[name="pt_phone"]').val(result.patient.phone);
				$('[name="pt_age"]').val(result.patient.age);
				$('[name="pt_gender"]').val(result.patient.pt_gender);
				$('[name="pt_village"]').val(result.patient.address_village);
				$('[name="pt_commune"]').val(result.patient.address_commune);
				
				endLoadScript = function () { $('[name="pt_district_id"]').val(result.patient.address_district_id).trigger('change'); } // set task for function waiting for execute
				$('[name="pt_province_id"]').val(result.patient.address_province_id).trigger('change');
			});
		}
		
	});



</script>
@endsection