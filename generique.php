<?php

// improvements on this code (good practises, etc...) are very welcomed (and encourraged), I wrote this code while learning PHP, so it's like a beginner code...!

global $id;
$GLOBALS["id"] = 1;

function afficherTableau($conn, $wordListSTR, $wordListARRAY, $type, $doc, $word)
{
	if ($type == "texts") {
		afficherTableauTextes($conn, $wordListSTR, $wordListARRAY);
	}
	else
	if ($type == "relatedKewWords") {
		afficherTableauKeyWords($conn, $wordListSTR);
	}
	else
	if ($type == "word_pos") {
		afficherWordPositions($conn, $doc, $word);
	}
}

function formatAdditionalColumns(){
	$result = '';
	foreach($GLOBALS['autresColonnes'] as $col){
		$result = $result.', b."'.$col.'"';
	}
	return $result;
}

function afficherTableauTextes($conn, $listParam, $wordListARRAY)
{
	//pg_query($conn, "SET search_path = '" . $GLOBALS["schema"] . "', pg_catalog;");
	$requete = '			select 
									url,
									title,
									mc,
									id_doc,
									bb.weight
								from
									(select  
										url 	,
										a.corpus as corp,
										title 	,
										id_doc
									from 	
										assoc a, 
										docs b
									where 	
										b.id_doc=a.id_document and 
										a.corpus = \'' . $GLOBALS['schema'] . '\' and
										b.corpus = \'' . $GLOBALS['schema'] . '\' and
										a.mc in (' . $listParam . ') 
									group by 
										url,title,corp,id_doc
									having 
										count(*)>=(select count(*) from (select mc from mc where corpus=\'' . $GLOBALS['schema'] . '\' and mc in (' . $listParam . ') )aa))aa,
									assoc bb
								where
									aa.id_doc = bb.id_document and
									bb.corpus = \'' . $GLOBALS['schema'] . '\'
								order by id_document,-bb.weight';

	if(count($GLOBALS['autresColonnes']) > 0){
		$formatedAdditionalColumns = formatAdditionalColumns();
		$relatedTable = $GLOBALS["schema"].'___add_docs';
		$requete = 'SELECT a.* '.$formatedAdditionalColumns.' from ('.$requete.') a, '.$relatedTable.' b WHERE a.id_doc = b.id_doc';
	} 	

	$ptr = pg_query($conn, $requete);

	//echo $requete;

	echo '<table class="textTab">';
	$i = pg_num_fields($ptr);
	$empty_result = true;
	$docPrecedent = "initial_value";
	$fermeture = "";
	$listemc = NULL;
	while ($ligne = pg_fetch_array($ptr)) {
		$linkeuroparl = null;
		$linkvotewatch = null;
		$texttype = null;
		$empty_result = false;
		if ($docPrecedent != $ligne["id_doc"]) { // AFFICHAGE DE LA LIGNE DES CARACTERISTIQUES DU TEXTE

			// AFFICHAGE DES MOTS CLES

			affichageMC($listemc, $GLOBALS["nbligneskw"], $wordListARRAY, $docPrecedent);
			$listemc = NULL;
			echo $fermeture; // on ferme la ligne precedente

			// Ouverture d'une nouvelle ligne

			echo '<tr>';
			echo '<td class=textColumn><a title="' . $GLOBALS["onTitleOver"] . '" href=\'' . $ligne["url"] . '\' target="_blank">' . $ligne["title"] . '</a></td>';
			eval($GLOBALS["additionalCols"]);
			$listemc[1] = $ligne["mc"];
			echo '<td class=kewWordsColumn>';
		}
		else {
			$listemc[sizeof($listemc) + 1] = $ligne["mc"];
		}

		$fermeture = '</td></tr>';
		$docPrecedent = $ligne["id_doc"];
	}

	if ($empty_result) {
		echo '<label class=info>No result matching all the keywords. Plese delete some keywords.</label>';
	}
	else {
		affichageMC($listemc, $GLOBALS["nbligneskw"], $wordListARRAY, $docPrecedent);
		echo $fermeture;
	}

	echo '</table>';
}

function afficherTableauKeyWords($conn, $listParam)
{
	$requete = '			select 
									mc,
									count(*) as cnt 
								from
									(select  
										id_document as "id"
									from 	
										assoc a
									where 	
										mc in (' . $listParam . ')  and
										corpus=\'' . $GLOBALS['schema'] . '\'
									group by 
										id_document
									having 
										count(*)>=(select count(*) from (select distinct mc from assoc where corpus=\'' . $GLOBALS['schema'] . '\' and mc in (' . $listParam . ') )aa)
									)aa,
									assoc bb
								where
									aa.id = bb.id_document
									and mc not in (' . $listParam . ') and
									bb.corpus=\''.$GLOBALS['schema'].'\'
								group by mc
								order by cnt desc,mc	';//todo: change 'schema' into 'corpus'
	$ptr = pg_query($conn, $requete);

	// echo $requete;

	echo '<table class="kwTab">';
	$i = pg_num_fields($ptr);
	$empty_result = true;
	while ($ligne = pg_fetch_array($ptr)) {
		$empty_result = false;
		$label_id = "relatedkw_" . $GLOBALS["id"];
		$echoKeyWord = "<a href=#           onclick='addKeyWordAndUpdateView(\"" . $ligne["mc"] . "\")'><label class=keywordSmall id='" . $label_id . "'>" . $ligne["mc"] . "</label></a>";
		$GLOBALS["id"] = $GLOBALS["id"] + 1;
		echo "<tr   onmouseout='keywordMouseOut()' onmouseover='keywordMouseOver(\"" . $label_id . "\")' >";
		echo '<td class=kewWordsColumn>' . $echoKeyWord . '</td>';
		echo '<td class=otherColumn>(' . $ligne["cnt"] . ')</td>';
		echo '</tr>';
	}

	if ($empty_result) {
		echo '<label class=info>No related keyword.</label>';
	}

	echo '</table>';
}

function affichageMC($listemc, $nbligneskw, $wordListARRAY, $id_doc)
{
	$len = 0;
	$lencpt = 0;
	for ($i = 0; $i < sizeof($listemc); $i++) { // on sort tous les mots clés accumulés lors du document précédent
		$len+= strlen($listemc[$i + 1]); //on calule leur nombre de caractères
	}

	$maxChar = $len / $nbligneskw;
	for ($i = 0; $i < sizeof($listemc); $i++) { // on sort tous les mots clés accumulés lors du document précédent
		$lencpt+= ($strlength = strlen($listemc[$i + 1])); //on calule leur nombre de caractères
		if ($lencpt > $maxChar) {
			$reste_a_combler = $maxChar - $lencpt + $strlength;
			if ($reste_a_combler < ($strlength / 2)) {
				echo "<br/>";
				$lencpt = 0;
			}
		}

		$selected = false;
		for ($j = 0; $j < sizeof($wordListARRAY); $j++) {
			if ($listemc[$i + 1] == $wordListARRAY[$j]) $selected = true;
		}

		$label_id = "textnav_" . $GLOBALS["id"];
		if ($selected) {
			echo "<a href=# onmouseout='hide_occurencies()' onmouseover='display_occurencies(\"" . $id_doc . "\",\"" . $listemc[$i + 1] . "\")'><label class=keywordSmallSelected id='" . $label_id . "' >" . $listemc[$i + 1] . "</label></a>";
		}
		else {
			echo "<a href=# onmouseout='keywordMouseOut();hide_occurencies()' onmouseover='keywordMouseOver(\"" . $label_id . "\");display_occurencies(\"" . $id_doc . "\",\"" . $listemc[$i + 1] . "\")' onclick='addKeyWordAndUpdateView(\"" . $listemc[$i + 1] . "\");hide_occurencies()'><label class=keywordSmall         id='" . $label_id . "' >" . $listemc[$i + 1] . "</label></a>";
		}

		$GLOBALS["id"] = $GLOBALS["id"] + 1;
	}
}

function afficherWordPositions($conn, $doc, $word)
{
	$requete = '			select title from docs where corpus=\''.$GLOBALS['schema'].'\' and id_doc=\'' . $doc . '\' ;';
	$ptr = pg_query($conn, $requete);
	$ligne = pg_fetch_array($ptr);
	echo "<h2><a>" . $ligne["title"] . "</a></h2><br />";
	/*$pos_list = array(); */
	$pos = array();
	$length = array();
	$better = array();
	$requete = '			select pos from pos_table where corpus=\''.$GLOBALS['schema'].'\' and id_doc=\'' . $doc . '\' and word=\'' . $word . '\';';
	$ptr = pg_query($conn, $requete);

	// echo $requete;

	$j = 0;

	// $i = pg_num_fields($ptr);

	$empty_result = true;
	$aa = 80;
	while ($ligne = pg_fetch_array($ptr)) {
		$empty_result = false;
		/*$pos_list[$j] = $ligne["pos"];*/
		$tmp = preg_split("/_/", $ligne["pos"]);
		$pos[$j] = $tmp[0]; //a remplacer a terme par $better
		$length[$j] = $tmp[1]; //a remplacer a terme par $better
		$better[$tmp[0]] = $tmp[1];
		$j = $j + 1;
	}

	ksort($better); // tri de $better selon les clés (qui sont les positions) ========innutile?
	$better_keys = array_keys($better);
	sort($better_keys, $sort_flags = SORT_NUMERIC);
	mb_internal_encoding("UTF-8"); // for the mb_substr function
	if (isset($GLOBALS["printAllTheText"])) {
		$requete = 'select "Texte" as t from docs_contenu where corpus=\''.$GLOBALS['schema'].'\' and id_doc=\'' . $doc . '\' ;';
		$ptr2 = pg_query($conn, $requete);
		$ligne = pg_fetch_array($ptr2);
		$text = $ligne["t"];
		$new_text = "<em>";
		$from = 0;
		for ($k = 0; $k < $j; $k++) {
			$new_text = $new_text . mb_substr($text, $from, $better_keys[$k] - 1 - $from) . "</em><label class=keywordSmallMouseOverTextnav>" . mb_substr($text, $better_keys[$k] - 1, $better[$better_keys[$k]]) . "</label><em>";
			$from = $better_keys[$k] + $better[$better_keys[$k]] - 1;
		}

		$new_text = $new_text . mb_substr($text, $from);
		echo $new_text;
	}
	else {
		for ($k = 0; $k < $j; $k++) {
			$requete = 'select substr("Texte",' . ($pos[$k] - $aa) . ',' . $aa . ') as txta,
					           substr("Texte",' . $pos[$k] . ',' . $length[$k] . ') as txtb,
					           substr("Texte",' . ($pos[$k] + $length[$k]) . ',' . $aa . ') as txtc from docs_contenu where id_doc=\'' . $doc . '\' and corpus=\''.$GLOBALS['schema'].'\';';
			$ptr2 = pg_query($conn, $requete);
			$ligne = pg_fetch_array($ptr2);
			echo "<em>[...] " . $ligne["txta"] . "</em><label class=keywordSmallMouseOverTextnav>" . $ligne["txtb"] . "</label><em>" . $ligne["txtc"] . " [...]</em>";
			echo "<br /><br />";
		}
	}

	if ($empty_result) {
		echo 'No availiable data...';
	}

	// echo $aa[1];

}

?>


 

