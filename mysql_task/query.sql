SELECT types.name AS type_name, COUNT(bikes.id) AS active_bikes
FROM motorcycle_types AS types
LEFT JOIN motorcycles AS bikes
ON types.id = bikes.type_id AND bikes.discontinued = 0
GROUP BY types.id, types.name;