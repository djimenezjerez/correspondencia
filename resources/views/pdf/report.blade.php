<!DOCTYPE html>
<html>
<head>
	<title>{{ $filename }}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
	<table class="fuente10">
		<tbody>
			<tr>
				<td width="36%">
					<div style="padding-bottom: 10px;">
						ASOCIACIÓN NACIONAL DE SUBOFICIALES Y SARGENTOS
					</div>
					<div style="padding-bottom: 10px;">
						DE LAS FUERZAS ARMADAS DEL ESTADO
					</div>
					<div class="negrilla">
						&ldquo;ASCINALSS&rdquo;
					</div>
				</td>
				<td width="53%"></td>
				<td width="21%">
					<div style="height: 70px; width: 100%; margin-top: 3px; margin-bottom: 1px;">
						<img src="{{ $logo }}" id="logo" alt="Logo" style="height: 100%; width: 60%; object-fit: cover;"/>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<div class="negrilla fuente16" style="margin-top: 24px; margin-bottom: 24px;">
						REPORTE SEGÚN CORRESPONDENCIA {{ $type }}
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<table class="fuente12">
						<tbody>
							<tr>
								<td width="9%" class="overflow">Del</td>
								<td width="25%" class="punteado">{{ $dates[0] }}</td>
								<td width="8%">al</td>
								<td width="25%" class="punteado">{{ $dates[1] }}</td>
								<td width="33%"></td>
							</tr>
						</tbody>
					</table>
				</td>
				<td colspan="2"></td>
			</tr>
		</tbody>
		<table class="table" style="margin-top: 24px;">
			<tbody>
				<tr class="negrilla fuente12">
					<td width="6%" class="borde">Nº</td>
					<td width="10%" class="borde">Hoja de Ruta</td>
					<td width="10%" class="borde">Tipo de Trámite</td>
					<td width="21%" class="borde">Procedencia</td>
					<td width="15%" class="borde">Detalle / Asunto</td>
					<td width="18%" class="borde">Funcionario Anterior / Área</td>
					<td width="18%" class="borde">Funcionario Actual / Área</td>
				</tr>
				@foreach ($procedures as $procedure)
					<tr class="overflow fuente10">
						<td class="borde overflow" style="text-align: center;">{{ $procedure['counter'] }}</td>
						<td class="borde overflow" style="text-align: center;">
							@foreach ($procedure['code'] as $line)
								<div>{{ $line }}</div>
							@endforeach
						</td>
						<td class="borde overflow" style="text-align: center;">
							@foreach ($procedure['procedure_type'] as $line)
								<div>{{ $line }}</div>
							@endforeach
						</td>
						<td class="borde overflow">
							@foreach ($procedure['origin'] as $line)
								<div>{{ $line }}</div>
							@endforeach
						</td>
						<td class="borde overflow">
							@foreach ($procedure['detail'] as $line)
								<div>{{ $line }}</div>
							@endforeach
						</td>
						<td class="borde overflow" style="text-align: center;">
							@foreach ($procedure['from'] as $line)
								<div>{{ $line }}</div>
							@endforeach
						</td>
						<td class="borde overflow" style="text-align: center;">
							@foreach ($procedure['current'] as $line)
								<div>{{ $line }}</div>
							@endforeach
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</table>
	<script type="text/php">
		if (isset($pdf)) {
			$text = "{PAGE_NUM} - {PAGE_COUNT}";
			$size = 10;
			$font = $fontMetrics->getFont("Arial");
			$width = $fontMetrics->get_text_width($text, $font, $size) / 2;
			$x = $pdf->get_width() / 2;
			$y = $pdf->get_height() - 45;
			$pdf->page_text($x, $y, $text, $font, $size);
		}
	</script>
</body>
<style>
@font-face {
	font-family: 'Arial';
	font-weight: normal;
	font-style: normal;
	font-variant: normal;
	src: url({{ storage_path('fonts/Arial.ttf') }}) format('truetype');
}
@page {
	margin: 3cm 2cm 2cm 2cm;
}
body {
	font-family: Arial, sans-serif;
}
table {
	width: 100%;
	table-layout: fixed;
	margin: 0px;
}
table, td {
	border-collapse: collapse;
}
table td {
    text-align: center;
}
.negrilla {
	font-weight: bold;
}
.fuente8 {
	font-size: 8px;
}
.fuente10 {
	font-size: 10px;
}
.fuente12 {
	font-size: 12px;
}
.fuente16 {
	font-size: 16px;
}
.borde {
	border: 1px solid black;
}
.punteado {
	border-bottom: 1px dotted black;
}
.overflow {
	text-align: left;
	overflow: hidden;
	word-break: keep-all;
	white-space: nowrap;
}
.padding_x {
	padding-left: 5px;
	padding-right: 5px;
}
.padding_y {
	padding-top: 3px;
	padding-bottom: 3px;
}
ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
li:before {
	content: '\2610';
	padding-right: 10px;
}
.seleccionado:before {
	content: '\25D9' !important;
	padding-right: 3px;
	padding-left: 1px;
}
</style>
</html>