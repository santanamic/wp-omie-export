<?php
/**
 * Page callback
 *
 * @since 1.0.0
 */
?>

<?php do_action( 'omie_export_admin_before' ); ?>
<div class="wrap about-wrap full-width-layout">
	<form method="POST" action="">
		<div class="content-form">
			<h1 class="about-title">
				<img class="about-logo" src="
					<?php echo OMIE_EXPORT_URI . '/assets/img/logo-omie.png' ?>" alt="
					<?php echo OMIE_EXPORT_TITLE ?>">
				<sup> 
					<?php echo OMIE_EXPORT_VERSION; ?> 
				</sup>
			</h1>
			<br>
			<h2 class="nav-tab-wrapper">
				<a href="#" class="nav-tab nav-tab-active">
					<strong>Export now</strong>
				</a>
			</h2>
			<p>
				<strong>Exportador Omie</strong>
			</p>
			<div id="content-left" style="float: left; width: 49%; max-width: 500px;">
				<div id="content-export-type-field" class="content-block">
					<div>
						<strong>Conteúdo exportado:</strong>
					</div>
					<label>
						<input type="checkbox" checked id="omie-export-type-os" name="omie-export-type-os" value="os"> Ordem de serviços / Vendas </label>
					<label style="margin-left: 10px" title="You will export only paid orders">
						<input type="checkbox" checked id="omie-export-type-cli" name="omie-export-type-cli" value="cli"> Usuários da plataforma </label>
				</div>
				<br>
				<div id="content-export-date-field" class="content-block">
					<div>
						<strong>Agrupamento de ordens:</strong>
					</div>
					<label for="omie-export-start-date">Quantidade por usuário <input type="number" id="omie-export-os-group-number" style="width: calc( 80% - 100px );min-width: 100px;text-align: right;" name="omie-export-os-group-number" value="1000">
					</label>
				</div>
				<br>
				<div id="content-export-date-field" class="content-block">
					<div>
						<strong>Nome do arquivo exportado:</strong>
					</div>
					<input type="text" name="omie-export-export-filename" style="width: 95%" value="%y-%m-%d.zip">
				</div>
				<br>
				<input id="omie-export-start-filter-id" name="omie-export-start-filter-id" type="hidden" value="0">
				<input id="omie-export-submit" name="omie-export-submit" type="submit" value="Exportar dados" class="button">
			</div>
			<div id="content-right" style="float: left; width: 48%; margin: 0px 20px; max-width: 500px;">
				<div id="content-export-date-field" class="content-block">
					<div>
						<strong style="font-size: 14px;/* font-weight: bold; */text-decoration: underline;">Filtrar por vendedor</strong>
						<span style="font-size: 9px;margin-left: 2px;">▲</span>
					</div>
					<div>
						<strong>Usuários por ID:</strong>
					</div>
					<input style="width: 95%" type="text" id="omie-export-filter-specific-users" name="omie-export-filter-specific-users" placeholder="Separe cada ID de usuário por uma vírgula. Ex: 1,56,412">
				</div>
				<br>
				<div id="content-export-date-field" class="content-block">
					<div>
						<strong style="font-size: 14px;/* font-weight: bold; */text-decoration: underline;">Filtrar por data</strong>
						<span style="font-size: 9px;margin-left: 2px;">▲</span>
					</div>
					<div>
						<strong>Utilizar um intervalo de datas:</strong>
					</div>
					<label for="omie-export-start-date">Data Inicial <input type="date" id="omie-export-start-date" name="omie-export-start-date">
					</label>
					<label for="omie-export-end-date">Data Final <input type="date" id="omie-export-end-date" name="omie-export-end-date">
					</label>
				</div>
				<br>
				<input id="omie-export-filter" name="omie-export-filter" type="hidden" value="continue">
			</div>
		</div>
	</form>
</div>
<?php do_action( 'omie_export_admin_after' ); ?>