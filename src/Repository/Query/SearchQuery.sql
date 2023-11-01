SELECT
    e.id,e.name as name , e.address,
    (
        SELECT ARRAY_AGG(sr.name)
        FROM service_establishment s
                 LEFT JOIN service sr ON s.service_id = sr.id
        WHERE s.establishment_id = e.id
    ) AS services,
    (
        SELECT ARRAY_AGG(sr.id)
        FROM service_category_establishment sc
                 LEFT JOIN service_category sr ON sc.service_category_id = sr.id
        WHERE sc.establishment_id = e.id
    ) AS Category,
    (
        SELECT avg(f.note)
        FROM feedback f
        WHERE f.establishment_id = e.id
    ) AS moyenne,
    (
        SELECT count(f.note)
        FROM feedback f
        WHERE f.establishment_id = e.id
    ) AS note_count,
    (6371 * ACOS(
                    COS(RADIANS(:lat)) * COS(RADIANS(e.latitude)) * COS(RADIANS(e.longitude) - RADIANS(:lng)) +
                    SIN(RADIANS(:lat)) * SIN(RADIANS(e.latitude))
            )) AS distance
FROM
    establishment e
WHERE
        (6371 * ACOS(
                        COS(RADIANS(:lat)) * COS(RADIANS(e.latitude)) * COS(RADIANS(e.longitude) - RADIANS(:lng)) +
                        SIN(RADIANS(:lat)) * SIN(RADIANS(e.latitude))
                )) <= :radius
ORDER BY distance