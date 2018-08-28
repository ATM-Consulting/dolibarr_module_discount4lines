<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2016 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2017      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class to manage Discount4Lines module actions
 *
 *
 */
class ActionsDiscount4lines
{

	function __construct($db)
	{
		global $langs;

		$this->db = $db;
		$langs->load('discount4lines@discount4lines');
	}

	/** Overloading the formConfirm function : replacing the parent's function with the one below
	 * @param      $parameters  array           meta datas of the hook (context, etc...)
	 * @param      $object      CommonObject    the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param      $action      string          current action (if set). Generally create or edit or null
	 * @param      $hookmanager HookManager     current hook manager
	 * @return     void
	 */
	function formConfirm($parameters, &$object, &$action, $hookmanager)
	{
		global $langs,$db,$user, $conf;
		
		$error = 0;

		$langs->load('discount4lines@discount4lines');

		$contexts = explode(':',$parameters['context']);

		if(in_array('propalcard',$contexts) || in_array('invoicecard',$contexts) || in_array('ordercard',$contexts) || in_array('ordersuppliercard', $contexts) || in_array('invoicesuppliercard', $contexts)) {
			
			$userHasRights = ! empty($user->rights->{$object->element}->creer);
			if ($object->element == 'order_supplier') $userHasRights = ! empty($user->rights->fournisseur->commande->creer);
			if ($object->element == 'invoice_supplier') $userHasRights = ! empty($user->rights->fournisseur->facture->creer);
			
			if ($object->statut == 0  && $userHasRights) {
				
				if($action == 'ask_discount4lines') {
					$form = new Form($this->db);
					
					$actionform = 'discount4lines';
					$title = $langs->trans('ApplyDiscount4linesTitle');
					$question = '';
					$formquestion = array(
						array('label'=> $langs->trans('EnterDiscountToApplyToEachLines'), 'name' => 'amount_discount4lines', 'type' => 'text', 'size' => 3)
					);
					$selectedchoice = 'yes';
					$useajax = 1;
					$out = $form->formconfirm($_SERVER['PHP_SELF'].'?ref='.$object->ref, $title, $question, $actionform, $formquestion, $selectedchoice, $useajax);
					
					if (! $error)
					{
						$this->results = array();
						$this->resprints = $out;
						
						return 0; // or return 1 to replace standard code
					}
					else
					{
						$this->errors[] = 'Error message';
						return -1;
					}
				}
			
				
				
			}
		}
	}
	
	/** Overloading the doActions function : replacing the parent's function with the one below
	 * @param      $parameters  array           meta datas of the hook (context, etc...)
	 * @param      $object      CommonObject    the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param      $action      string          current action (if set). Generally create or edit or null
	 * @param      $hookmanager HookManager     current hook manager
	 * @return     void
	 */
	function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $langs,$db,$user, $conf;
	
		$langs->load('discount4lines@discount4lines');
	
		$contexts = explode(':',$parameters['context']);
		if( in_array('propalcard',$contexts) || in_array('invoicecard',$contexts) || in_array('ordercard', $contexts) || in_array('ordersuppliercard', $contexts) || in_array('invoicesuppliercard', $contexts)) {

			$userHasRights = ! empty($user->rights->{$object->element}->creer);
			if ($object->element == 'order_supplier') $userHasRights = ! empty($user->rights->fournisseur->commande->creer);
			if ($object->element == 'invoice_supplier') $userHasRights = ! empty($user->rights->fournisseur->facture->creer);

			if ($object->statut == 0  && $userHasRights) {
				if($action == 'discount4lines') {
					
					$countLineUpdated = 0;
					$err = 0;
					
					foreach($object->lines as $line) {
						if(
								($line->product_type == '0' && ! empty($conf->global->DISCOUNT4LINES_APPLY_TO_PRODUCTS))	// Si produit et qu'on applique la réduc sur les produits
								|| ($line->product_type == '1' && ! empty($conf->global->DISCOUNT4LINES_APPLY_TO_SERVICES))	// Si service et qu'on applique la réduc sur les services
						)
						{
							$remise_percent = GETPOST('amount_discount4lines','int');
							if($line->total_ht > 0) {
								
								if(in_array('propalcard',$contexts)) {
								
									$res = $object->updateline(
										$line->id,
									isset($line->subprice) ? $line->subprice : $line->price,
										$line->qty,
										$remise_percent,
										$line->tva_tx,
										$line->localtax1_tx,
										$line->localtax2_tx,
										$line->desc,
										$line->price_base_type,
										$line->infobits,
										$line->special_code,
										$line->fk_parent_line,
										$line->skip_update_total,
										$line->fk_fournprice,
										$line->pa_ht,
										$line->label,
										$line->product_type,
										$line->date_start,
										$line->date_end,
										$line->array_options,
									$line->fk_unit,
									$line->multicurrency_subprice
									);
								} elseif(in_array('invoicecard',$contexts)) {
									$res = $object->updateline(
										$line->id, 
										$line->desc, 
									isset($line->subprice) ? $line->subprice : $line->price,
										$line->qty, 
										$remise_percent, 
										$line->date_start, 
										$line->date_end, 
										$line->tva_tx, 
										$line->localtax1_tx, 
										$line->localtax2_tx, 
										$line->price_base_type, 
										$line->infobits, 
										$line->product_type, // type
										$line->fk_parent_line, 
										$line->skip_update_total, 
										$line->fk_fournprice, 
										$line->pa_ht=0, 
										$line->label, 
										$line->special_code, 
										$line->array_options,
										$line->situation_percent,
										$line->fk_unit,
										$line->multicurrency_subprice
									);
								} elseif(in_array('ordercard', $contexts)) {
									$res = $object->updateline(
										$line->rowid,
										$line->desc,
									isset($line->subprice) ? $line->subprice : $line->price,
										$line->qty,
										$remise_percent,
										$line->tva_tx,
										$line->localtax1_tx,
										$line->localtax2_tx,
										'HT',
										$line->info_bits,
										$line->date_start,
										$line->date_end,
										$line->product_type,
										$line->fk_parent_line,
										$line->skip_update_total,
										$line->fk_fournprice,
										$line->pa_ht,
										$line->label,
										$line->special_code,
										$line->array_options,
										$line->fk_unit
									);
								} elseif(in_array('ordersuppliercard', $contexts)) {
									$res = $object->updateline(
										$line->id,
										$line->desc,
										$line->pu_ht,
										$line->qty,
										$remise_percent,
										$line->tva_tx,
										$line->localtax1_tx,
										$line->localtax2_tx,
										'HT',
										$line->info_bits,
										$line->product_type,
										false,
										$line->date_start,
										$line->date_end,
										$line->array_options,
										$line->fk_unit
									);
								} elseif(in_array('invoicesuppliercard', $contexts)) {
									$res = $object->updateline(
										$line->id,
										$line->description,
										$line->pu_ht,
										$line->tva_tx,
										$line->localtax1_tx,
										$line->localtax2_tx,
										$line->qty,
										$line->fk_product,
										'HT',
										$line->info_bits,
										$line->product_type,
										$remise_percent,
										false,
										'',
										'',
										$line->array_options,
										$line->fk_unit
									);
								}

								if($res > 0) {
									$countLineUpdated++;
								} else {
									$err++;
								}
							}
						}
					}

					if($countLineUpdated > 0) {
						setEventMessage($langs->trans('Discount4linesApplied', $countLineUpdated));
					}
					
					// Si commande fournisseur, il n'y a pas de redirection, donc l'affichage des lignes n'est pas mis à jour : on redirige
					if(in_array('ordersuppliercard', $contexts)) {
						header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
						exit;
					}
				}
			}
		}
	}
	
	
			

	function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager) {
		
		global $langs,$db,$user, $conf;
		
		$contexts = explode(':',$parameters['context']);
		
		if( in_array('propalcard',$contexts) || in_array('invoicecard',$contexts) || in_array('ordercard',$contexts) || in_array('ordersuppliercard', $contexts) || in_array('invoicesuppliercard', $contexts)) {

			$userHasRights = ! empty($user->rights->{$object->element}->creer);
			if ($object->element == 'order_supplier') $userHasRights = ! empty($user->rights->fournisseur->commande->creer);
			if ($object->element == 'invoice_supplier') $userHasRights = ! empty($user->rights->fournisseur->facture->creer);
				
			if ($object->statut == 0  && $userHasRights) {
		
				$out = '<div class="inline-block divButAction"><a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=ask_discount4lines">' . $langs->trans('BtnDiscount4Lines') . '</a></div>';
			
				$this->results = array();
				$this->resprints = $out;
				print $out;
				return 0; // or return 1 to replace standard code
			}
		}	
	}
}
