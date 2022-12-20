<?php 
	$liaison2 = mysqli_connect('127.0.0.1', 'gStock', 'gS123k12');
	mysqli_select_db($liaison2, 'stocks');
	
		if(isset($_POST["param"]))
		{		
			switch($_POST["param"])
			{
				case "recup_client":
					$requete = "SELECT * FROM clients WHERE Client_num = ".$_POST["ref_client"].";";
					$retours = mysqli_query($liaison2, $requete);
					$retour = mysqli_fetch_array($retours);
					$chaine = $retour["Client_civilite"]."|".$retour["Client_nom"]."|".$retour["Client_prenom"];
					print($chaine);					
				break;
				
				case "recup_article":
					$requete = "SELECT * FROM articles WHERE Article_code = '".$_POST["ref_produit"]."';";
					$retours = mysqli_query($liaison2, $requete);
					$retour = mysqli_fetch_array($retours);
					$chaine = $retour["Article_designation"]."|".$retour["Article_PUHT"]."|".$retour["Article_Qte"];	
					print($chaine);					
				break;

				case "creer_client":
					$requete = "SELECT COUNT(Client_num) AS nb FROM clients WHERE Client_nom='".$_POST["nom_client"]."' AND Client_prenom='".$_POST["prenom_client"]."';";
					$retours = mysqli_query($liaison2, $requete);
					$retour = mysqli_fetch_array($retours);
					if($retour["nb"]>0)	
						print("nok");
					else
					{
						$requete = "INSERT INTO clients(Client_civilite, Client_nom, Client_prenom) VALUES ('".$_POST["civilite"]."', '".$_POST["nom_client"]."', '".$_POST["prenom_client"]."');";
						$retours = mysqli_query($liaison2, $requete);
						if($retours==1)
							print(mysqli_insert_id($liaison2));
					}
				break;
				case "facturer":
					$com_client = $_POST["ref_client"];
					$com_date = date('d/m/Y');
					$com_montant = $_POST["total_com"];
					
					$texte_com = $_POST["chaine_com"];
					$tab_com=explode('|',$texte_com);

					$requete = "INSERT INTO commandes(Com_client, Com_date, Com_montant) VALUES (".$com_client.", '".$com_date."', ".$com_montant.");";
					$retours = mysqli_query($liaison2, $requete);
					if($retours==1)
					{
						$detail_com = mysqli_insert_id($liaison2);
						for($ligne=0 ;$ligne<sizeof($tab_com) ;$ligne++)
						{
							if($tab_com[$ligne]!="")
							{
								$ligne_com = explode(';',$tab_com[$ligne]);
								$requete = "INSERT INTO detail(Detail_com, Detail_ref, Detail_qte) VALUES (".$detail_com.", '".$ligne_com[0]."', ".$ligne_com[1].");";
								$retours = mysqli_query($liaison2, $requete);
								$requete = "UPDATE articles SET Article_Qte=Article_Qte-".$ligne_com[1]." WHERE Article_code='".$ligne_com[0]."';";
								$retours = mysqli_query($liaison2, $requete);									
							}						
						}
						print($com_client."-".$detail_com);
					}
					else
						print("nok");
					
				break;			
			}
		}
	
	mysqli_close($liaison2);
?>