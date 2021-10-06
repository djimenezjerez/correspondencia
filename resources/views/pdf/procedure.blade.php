<!DOCTYPE html>
<html>
<head>
	<title>{{ $filename }}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
	<table class="borde fuente12">
		<tbody>
			<tr>
				<td width="33%" class="borde" style="height: 130px;">
					<div style="height: 80px; width: 100%; margin-top: 3px; margin-bottom: 1px;">
						<img src="{{ $logo }}" id="logo" alt="Logo" style="height: 100%; width: 60%; object-fit: cover;"/>
					</div>
					<div class="fuente8">
						ASOCIACIÓN NACIONAL DE SUBOFICIALES
					</div>
					<div class="fuente8">
						Y SARGENTOS DE LAS FUERZAS
					</div>
					<div class="fuente8">
						ARMADAS DEL ESTADO
					</div>
					<div class="fuente10 negrilla">
						&ldquo;ASCINALSS&rdquo;
					</div>
				</td>
				<td width="34%" class="borde fuente16 negrilla padding_x" style="height: 120px; vertical-align: top;">
					<div>
						HOJA DE RUTA
					</div>
					<div>
						<table>
							<tbody>
								<tr>
									<td width="10%" style="height: 50px; vertical-align: bottom;" class="overflow">Nº</td>
									<td width="90%" style="height: 50px; vertical-align: bottom; padding-top: 10px;" class="punteado overflow">{{ $procedure->code }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</td>
				<td width="33%" class="borde" style="height: 120px;"></td>
			</tr>
			<tr>
				<td colspan="3">
					<div class="negrilla" style="margin-top: 5px;">
						DOCUMENTO RECIBIDO
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="padding_x">
					<table class="padding_y">
						<tbody>
							<tr>
								<td width="4%" style="height: 15px;" class="negrilla overflow">
									Nº:
								</td>
								<td width="7%" style="height: 15px;" class="punteado overflow"></td>
								<td width="8%" style="height: 15px;" class="negrilla overflow">
									Remite:
								</td>
								<td width="64%" style="height: 15px;" class="punteado overflow">
									{{ $procedure->origin }}
								</td>
								<td width="7%" style="height: 15px;" class="negrilla overflow">
									Fecha:
								</td>
								<td width="10%" style="height: 15px;" class="punteado overflow">
									{{ \Carbon\Carbon::parse($procedure->created_at)->format('d/m/Y') }}
								</td>
							</tr>
						</tbody>
					</table>
					<table class="padding_y">
						<tbody>
							<tr>
								<td width="11%" style="height: 15px;" class="negrilla overflow">
									Referencia:
								</td>
								<td width="89%" style="height: 15px;" class="punteado overflow">
									{{ $procedure->detail[0] }}
								</td>
							</tr>
							<tr>
								<td colspan="2" width="100%" style="height: 15px;" class="punteado overflow">
									{{ $procedure->detail[1] ?? '' }}
								</td>
							</tr>
						</tbody>
					</table>
					<table class="padding_y" style="margin-bottom: 5px;">
						<tbody>
							<tr>
								<td width="8%" style="height: 15px;" class="negrilla overflow">
									Anexos:
								</td>
								<td width="52%" style="height: 15px;" class="punteado overflow"></td>
								<td width="8%" style="height: 15px;" class="negrilla overflow">
									Nº Fojas:
								</td>
								<td width="30%" style="height: 15px;" class="punteado overflow"></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				@foreach ($areas as $i => $chunk)
					<td class="borde fuente10 padding_x padding_y" style="{{ $areas_styles[$i] }}">
						<ul>
							@foreach ($chunk as $area)
								<li class="{{ $area->selected ? 'seleccionado' : '' }}">{{ $area->name }}</li>
							@endforeach
						</ul>
					</td>
				@endforeach
			</tr>
			@for ($i = 0; $i < 4; $i++)
				<tr>
					<td class="borde padding_x"></td>
					<td colspan="2" class="borde padding_x overflow padding_y">
						<div class="negrilla">
							Destino:
						</div>
						@for ($j = 0; $j < 4; $j++)
							<div class="punteado" style="width: 100%; height: 15px;"></div>
						@endfor
						<div class="negrilla padding_y" style="width: 100%; margin-top: 31px;">
							<span style="margin-right: 280px;">Firma</span>
							<span>Fecha-Hora</span>
						</div>
					</td>
				</tr>
			@endfor
		</tbody>
	</table>
</body>
<style>
@font-face {
	font-family: 'Elegance';
	font-weight: normal;
	font-style: normal;
	font-variant: normal;
	src: url({{ storage_path('fonts/Arial.ttf') }}) format('truetype');
}
@page {
	margin: 2cm;
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