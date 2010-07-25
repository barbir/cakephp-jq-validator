<?php
/*
This file is part of CakePHP JQValidator Plugin.
 
CakePHP JQValidator Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
 
CakePHP JQValidator Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with CakePHP JQValidator Plugin. If not, see <http://www.gnu.org/licenses/>.
*/

class JQValidatorComponent extends Object 
{
	function addValidation($modelName, $validationOptions, $errorMessageTitle = 'The data you entered failed to validate. Fix the following errors:', $formId = null)
	{
		if(!$formId)
			$formId = '';
		else
			$formId = '#' . $formId;

		$validations = Configure::read('JQValidator.jQValidations');

		if(!isset($validations))
			$validations = array();

		$validations[$formId] = array
		(
			'modelName' => $modelName,
			'validationOptions' => $validationOptions,
			'errorMessageTitle' => $errorMessageTitle,
			'formId' => $formId,
		);

		Configure::write('JQValidator.jQValidations', $validations);
	}
}
?>
