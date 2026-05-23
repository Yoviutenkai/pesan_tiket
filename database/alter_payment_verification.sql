-- payments table already updated; only apply remaining steps below
ALTER TABLE orders
  MODIFY status_pembayaran ENUM('pending','paid','rejected','expired','cancelled') DEFAULT 'pending',
  MODIFY status_order ENUM('menunggu','berhasil','dibatalkan') DEFAULT 'menunggu';

ALTER TABLE payments
  ADD CONSTRAINT fk_payments_verified_by FOREIGN KEY (verified_by) REFERENCES users(id_user) ON DELETE SET NULL;
