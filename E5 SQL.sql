SELECT user.nom, user.prenom, SUM(ligne_frais_forfait.quantite) as total_km
FROM user
INNER JOIN fiche_frais ON user.id = fiche_frais.user_id
INNER JOIN ligne_frais_forfait ON fiche_frais.id = ligne_frais_forfait.fiche_frais_id
INNER JOIN frais_forfait ON ligne_frais_forfait.frais_forfait_id = frais_forfait.id
WHERE fiche_frais.mois IN ('202307', '202308') AND frais_forfait.id = 2
GROUP BY user.id
HAVING total_km > 1500;