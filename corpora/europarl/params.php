

<?php
global $onTitleOver;
$onTitleOver = "View text on the Parliament website";
global $schema;
$schema = "europarl";
global $nbligneskw;
$nbligneskw = 3;
global $additionalCols;
$additionalCols = NULL;
$additionalCols = "echo ' 




<td class=\"otherColumn\"><a href='.\$ligne['url_votewatch'].' target=\"_blank\">
							  <img alt=\"VoteWatch\" src=\"http://www.votewatch.eu/favicon.ico\" title=\"See how Members of the European Parliament voted for this text on www.votewatch.eu\">
							  </img></a></td>

<td class=\"otherColumn\">
							  <a href='.\$ligne['url_votewatch'].' target=\"_blank\">
							  <img alt=\"'.\$ligne['policy'].'\" src=\"./corpora/europarl/img/'.\$ligne['policy'].'.png\" title=\"Policy area: '.\$ligne['policy'].'\">
							  </img></a></td>
';"     ; // very ugly! Feel free to fix it!
global $autresColonnes;
$autresColonnes = NULL;
$autresColonnes = ["date","policy","url_votewatch"];

?>

