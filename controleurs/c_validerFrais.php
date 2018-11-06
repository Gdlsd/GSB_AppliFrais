<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
$idMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);

switch($action){
    
    case 'afficherListeVisiteurMois':
        $lesVisiteurs = $pdo->getListeVisiteur();
        $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        $moisASelectionner = $idMois;
        include 'vues/v_listeVisiteurs.php';
        break;
    
    case 'corrigerFraisForfait' :

        $lesFrais = filter_input(INPUT_POST, 'lesFrais', 
                FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (lesQteFraisValides($lesFrais)) {
        $pdo->majFraisForfait($idVisiteur, $idMois, $lesFrais);     
        ajouterSucces('Modification des éléments forfaitisés enregistrée');
        include 'vues/v_succes.php';
        } else {
        ajouterErreur('Les valeurs des frais doivent être numériques');
        include 'vues/v_erreurs.php';
        }             
        $lesVisiteurs = $pdo->getListeVisiteur();
        $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        $moisASelectionner = $idMois;
        include 'vues/v_listeVisiteurs.php';
        break;
        
    case 'validationFraisHorsForfait' :
        
        if(isset($_POST['repporterFiche']))
        {
            $idMoisSuivant = getMoisSuivant($idMois);
            $libelle = filter_input(INPUT_POST, 'libelleFraisHf', FILTER_SANITIZE_STRING);
            $dateFrais = filter_input(INPUT_POST, 'dateFraisHf', FILTER_SANITIZE_STRING);
            $montant= filter_input(INPUT_POST, 'montantFraisHf', FILTER_SANITIZE_STRING);
        
        
        $pdo->creeNouvellesLignesFrais($idVisiteur, $idMoisSuivant);         
        
        $pdo->creeNouveauFraisHorsForfait(
        $idVisiteur,
        $idMoisSuivant,
        $libelle,
        $dateFrais,
        $montant
            );
        
        $idFrais = filter_input(INPUT_POST, 'idFrais', FILTER_SANITIZE_STRING);
        $pdo->supprimerFraisHorsForfait($idFrais);        
                    
            ajouterSucces('Les frais on été repporté au mois suivant');
            include 'vues/v_succes.php';
        }
        else
        {
            
            ajouterSucces('Les frais on été refusés');
            include 'vues/v_succes.php';
        }
        
        $lesVisiteurs = $pdo->getListeVisiteur();
        $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        $moisASelectionner = $idMois;
        include 'vues/v_listeVisiteurs.php';
        break;
}

$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $idMois);        
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $idMois);

if(!empty($lesFraisForfait) || !empty($lesFraisHorsForfait)){   
    include 'vues/v_validerFrais.php';
}
