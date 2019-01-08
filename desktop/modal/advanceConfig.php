<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
$eqLogic = eqLogic::byId(init('eqLogic_id'));
if (!is_object($eqLogic)) {
	throw new Exception(__('Eqlogic ID non valide : ', __FILE__) . init('eqLogic_id'));
}
$device = gsh_devices::byLinkTypeLinkId('eqLogic', $eqLogic->getId());
if (!is_object($device)) {
	throw new Exception(__('Device non trouvé', __FILE__));
}
if ($device->getType() == '') {
	throw new Exception(__('Aucun type configuré pour ce périphérique', __FILE__));
}
sendVarToJs('device', utils::o2a($device));
?>
<div id="div_alertAdvanceConfigure"></div>
<div id="div_advanceConfigForm">
	<input type="text" class="deviceAttr form-control" data-l1key="id" style="display : none;" />
	<?php
	switch ($device->getType()) {
		case 'action.devices.types.BLINDS':
		?>
		<a class="btn btn-success pull-right bt_advanceConfigSaveDevice">{{Sauvegarder}}</a>
		<legend>{{Configuration du volet}}</legend>
		<form class="form-horizontal">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label">{{Inverser}}</label>
					<div class="col-sm-3">
						<input type="checkbox" class="deviceAttr" data-l1key="options" data-l2key="blinds::invert"></input>
					</div>
				</div>
			</fieldset>
		</form>
		<?php
		break;
		case 'action.devices.types.SHUTTER':
		?>
		<a class="btn btn-success pull-right bt_advanceConfigSaveDevice">{{Sauvegarder}}</a>
		<legend>{{Configuration du volet}}</legend>
		<form class="form-horizontal">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label">{{Inverser}}</label>
					<div class="col-sm-3">
						<input type="checkbox" class="deviceAttr" data-l1key="options" data-l2key="shutter::invert"></input>
					</div>
				</div>
			</fieldset>
		</form>
		<?php
		break;
		case 'action.devices.types.THERMOSTAT':
		?>
		<a class="btn btn-success pull-right bt_advanceConfigSaveDevice">{{Sauvegarder}}</a>
		<legend>{{Configuration du thermostat}}</legend>
		<form class="form-horizontal">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label">{{Action pour le mode chaud}}</label>
					<div class="col-sm-3">
						<select class="form-control deviceAttr" data-l1key="options" data-l2key="thermostat::heat">
							<option value="">{{Aucun}}</option>
							<?php
							foreach ($eqLogic->getCmd('action', 'modeAction', null, true) as $cmd) {
								echo '<option value="' . $cmd->getId() . '">' . $cmd->getName() . '</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">{{Action pour le mode froid}}</label>
					<div class="col-sm-3">
						<select class="form-control deviceAttr" data-l1key="options" data-l2key="thermostat::cool">
							<option value="">{{Aucun}}</option>
							<?php
							foreach ($eqLogic->getCmd('action', 'modeAction', null, true) as $cmd) {
								echo '<option value="' . $cmd->getId() . '">' . $cmd->getName() . '</option>';
							}
							?>
						</select>
					</div>
				</div>
			</fieldset>
		</form>
		<?php
		break;
		default:
		echo '<div class="alert alert-info">{{Il n\'y a aucune configuration avancée pour ce type}}</div>';
		break;
	}
	?>
</div>

<script>
$('#div_advanceConfigForm').setValues(device, '.deviceAttr');
$('.bt_advanceConfigSaveDevice').on('click',function(){
	var device = $('#div_advanceConfigForm').getValues('.deviceAttr')[0];
	$.ajax({
		type: "POST",
		url: "plugins/gsh/core/ajax/gsh.ajax.php",
		data: {
			action: "saveDevice",
			device : json_encode(device),
		},
		dataType: 'json',
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alertAdvanceConfigure').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#div_alertAdvanceConfigure').showAlert({message: '{{Sauvegarde réussi, pensez à relancer une synchronisation}}', level: 'success'});
		},
	});
});

</script>
