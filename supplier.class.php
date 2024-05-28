<?php
/**
 * @version $Id: setup.php 378 2014-06-08 15:12:45Z yllen $
 -------------------------------------------------------------------------
 LICENSE

 This file is part of PDF plugin for GLPI.

 PDF is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 PDF is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with Reports. If not, see <http://www.gnu.org/licenses/>.

 @package   pdf
 @authors   Nelly Mahu-Lasson
 @copyright Copyright (c) 2020-2021 PDF plugin team
 @license   AGPL License 3.0 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://forge.glpi-project.org/projects/pdf
 @link      http://www.glpi-project.org/
 @since     2009
 --------------------------------------------------------------------------
*/


class PluginPdfSupplier extends PluginPdfCommon {

   static $rightname = "plugin_pdf";


   function __construct(CommonGLPI $obj=NULL) {
      $this->obj = ($obj ? $obj : new Supplier());
   }
   


   function defineAllTabsPDF($options=[]) {

      $onglets = parent::defineAllTabsPDF($options);
	 unset($onglets['KnowbaseItem_Item$1']);
	 unset($onglets['Notepad$1']);
	 unset($onglets['Ticket$1']);
	 unset($onglets['Change_Item$1']);
	 unset($onglets['Item_Problem$1']);
	 unset($onglets['Infocom$1']);
	 unset($onglets['Link$1']);
      return $onglets;
   }

// AFFICHE le contenu de l'onglet FOURNISSEUR 
   static function pdfMain(PluginPdfSimplePDF $pdf, Supplier $supplier){

      $dbu = new DbUtils();

      PluginPdfCommon::mainTitle($pdf, $supplier);

      $pdf->displayLine(
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Name').'</i></b>', $supplier->fields['name']),
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Phone').'</i></b>', $supplier->fields['phonenumber']));
      $pdf->displayLine(
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Address').'</i></b>', $supplier->fields['address']));
      $pdf->displayLine(
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Postal code').'</i></b>', $supplier->fields['postcode']),
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Town').'</i></b>', $supplier->fields['town']));
      $pdf->displayLine(
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Website').'</i></b>', $supplier->fields['website']),
         '<b><i>'.sprintf(__('%1$s: %2$s'), __('Email').'</i></b>', $supplier->fields['email']));

      PluginPdfCommon::mainLine($pdf, $supplier, 'comment');
      
      $pdf->displaySpace();
	    
   }

// AFFICHE le contenu de l'onglet CONTRAT et CONTACT
   static function displayTabContentForPDF(PluginPdfSimplePDF $pdf, CommonGLPI $item, $tab) {

      switch ($tab) {
         case 'Contact_Supplier$1' :
	    PluginPdfSupplier::pdfContact($pdf, $item);
            break;

         case 'Contract_Supplier$1' :
	    PluginPdfSupplier::pdfContract($pdf, $item);
            break;

         default :
           return false;
      }
      return true;
   }

// RECHERCHE les infos du CONTRAT
// SELECT * FROM glpi_contracts WHERE id IN (SELECT id FROM glpi_contracts_suppliers WHERE suppliers_id=$ID_supplier);

   static function pdfContract(PluginPdfSimplePDF $pdf, Supplier $supplier) {
      global $DB;

         $pdf->setColumnsSize(100);
         $pdf->displayTitle("<b><i>".__('Contracts')."</i></b>");
         
	 $pdf->setColumnsSize(100);
         $pdf->setColumnsSize(20,20,20,20,20);
         $pdf->setColumnsAlign('left','center','center','left', 'right');
         $pdf->displayTitle("<b><i>".__('Name')."</i></b>",
                            "<b><i>".__('Number')."</i></b>",
                            "<b><i>".__('Contract type')."</i></b>",
                            "<b><i>".__('Start date')."</i></b>",
                            "<b><i>".__('Initial Contract period')."</i></b>");

      $ID_supplier = $supplier->getField('id');
      $Rq_contract_supplier = $DB->request(['FROM'   => 'glpi_contracts_suppliers',
        	                       'WHERE'  => ['suppliers_id' => $ID_supplier]]); 
       while ($TAB_contract_supplier = $Rq_contract_supplier->next()) {
//          $pdf->displayLine(DEBUG_ID_du_Fournisseur,$TAB_contract_supplier['suppliers_id']);
//          $pdf->displayLine(DEBUG_ID_du_Contract,$TAB_contract_supplier['contracts_id']);
	    $id_contract = $TAB_contract_supplier['contracts_id'];

            $Rq_contracts = $DB->request(['FROM'   => 'glpi_contracts',
                                          'WHERE'  => ['id' => $id_contract]]);
            while ($TAB_contracts = $Rq_contracts->next()) {
		$contracttype = 0;
	      	$id_contracttype = $TAB_contracts['contracttypes_id'];
	      	$Rq_contracttypes = $DB->request(['FROM'   => 'glpi_contracttypes',
       	                                       'WHERE'  => ['id' => $id_contracttype]]); 
              	while ($TAB_contracttype = $Rq_contracttypes->next()) {
			  $contracttype = $TAB_contracttype['name'];
               	}
               $pdf->displayLine($TAB_contracts['name'],
                     Html::Clean($TAB_contracts['num']),
//                     Html::Clean($TAB_contracts['contracttypes_id']),
                     Html::Clean($contracttype),
                     Html::Clean($TAB_contracts['begin_date']),
                     Html::Clean($TAB_contracts['periodicity']));
             }
       }

      }
      
// RECHERCHE les infos du CONTACT

   static function pdfContact(PluginPdfSimplePDF $pdf, Supplier $supplier) {
      global $DB;
         
         $pdf->setColumnsSize(100);
         $pdf->displayTitle("<b><i>".__('Contacts')."</i></b>");
         
	 $pdf->setColumnsSize(100);
         $pdf->setColumnsSize(16.6,16.6,16.6,16.6,16.6,16.6);
         $pdf->setColumnsAlign('left','center','center','center','left', 'right');
         $pdf->displayTitle("<b><i>".__('Name')."</i></b>",
                            "<b><i>".__('First name')."</i></b>",
                            "<b><i>".__('Phone')."</i></b>",
                            "<b><i>".__('Mobile')."</i></b>",
                            "<b><i>".__('Email')."</i></b>",
                            "<b><i>".__('Contact type')."</i></b>");

      $ID_supplier = $supplier->getField('id');
      $Rq_contact_supplier = $DB->request(['FROM'   => 'glpi_contacts_suppliers',
        	                       'WHERE'  => ['suppliers_id' => $ID_supplier]]); 
       while ($TAB_contact_supplier = $Rq_contact_supplier->next()) {
//          $pdf->displayLine(DEBUG_ID_du_Fournisseur,$TAB_contract_supplier['suppliers_id']);
//          $pdf->displayLine(DEBUG_ID_du_Contract,$TAB_contract_supplier['contracts_id']);
 	    $id_contact = $TAB_contact_supplier['contacts_id'];
            
            $Rq_contacts = $DB->request(['FROM'   => 'glpi_contacts',
                                          'WHERE'  => ['id' => $id_contact]]);
            while ($TAB_contacts = $Rq_contacts->next()) {
		$contacttype = 0;
	      	$id_contacttype = $TAB_contacts['contacttypes_id'];
	      	$Rq_contacttypes = $DB->request(['FROM'   => 'glpi_contacttypes',
       	                                       'WHERE'  => ['id' => $id_contacttype]]); 
              	while ($TAB_contacttype = $Rq_contacttypes->next()) {
			  $contacttype = $TAB_contacttype['name'];
               	}
               $pdf->displayLine($TAB_contacts['name'],
                     Html::Clean($TAB_contacts['firstname']),
                     Html::Clean($TAB_contacts['phone']),
                     Html::Clean($TAB_contacts['mobile']),
                     Html::Clean($TAB_contacts['email']),
                     Html::Clean($contacttype));
             }
       }
      
       $pdf->displaySpace();

   }

}
