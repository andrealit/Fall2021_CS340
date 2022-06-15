-- Find the film_title of all films which feature both KIRSTEN PALTROW and WARREN NOLTE
-- Order the results by film_title in descending order.
(SELECT f.title AS film_title FROM film f
	INNER JOIN film_actor fa1 ON fa1.film_id = f.film_id
	INNER JOIN actor a1 ON a1.actor_id = fa1.actor_id
	INNER JOIN film_actor fa2 ON fa2.film_id = f.film_id
	INNER JOIN actor a2 ON a2.actor_id = fa2.actor_id
WHERE (a1.first_name = 'KIRSTEN' AND a1.last_name = 'PALTROW')
	AND (a2.first_name = 'WARREN' AND a2.last_name = 'NOLTE'))
ORDER BY f.title DESC;
