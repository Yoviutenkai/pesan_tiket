ALTER TABLE orders
  ADD COLUMN id_voucher INT NULL AFTER id_user,
  ADD CONSTRAINT fk_orders_voucher
    FOREIGN KEY (id_voucher)
    REFERENCES voucher(id_voucher)
    ON DELETE SET NULL
    ON UPDATE CASCADE;
