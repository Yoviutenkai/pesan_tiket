SELECT e.nama_event,
       COALESCE(SUM(od.qty), 0) AS total_tiket_terjual
FROM event e
LEFT JOIN tiket t ON t.id_event = e.id_event
LEFT JOIN order_detail od ON od.id_tiket = t.id_tiket
GROUP BY e.id_event, e.nama_event
ORDER BY total_tiket_terjual DESC;
