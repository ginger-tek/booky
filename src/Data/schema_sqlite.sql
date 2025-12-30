pragma foreign_keys=on;
create table if not exists users(
  id text primary key,
  username text not null unique,
  passhash text not null,
  email text not null unique,
  enabled integer default 0,
  created datetime default current_timestamp,
  updated datetime default current_timestamp
);
create table if not exists invoices(
  id text primary key,
  userId text not null,
  clientId text not null,
  summary text not null,
  details text,
  dueDate text,
  paidDate text,
  amountDue number default 0.00,
  amountPaid number default 0.00,
  created datetime default current_timestamp,
  updated datetime default current_timestamp
);
create table if not exists clients(
  id text primary key,
  userId text not null,
  name text not null,
  email text,
  phone text,
  address text,
  created datetime default current_timestamp,
  updated datetime default current_timestamp,
  foreign key(userId) references users(id) on delete cascade
);
create table if not exists invoice_items(
  id text primary key,
  userId text not null,
  invoiceId text not null,
  summary text not null,
  type text not null,
  amount number default 0.00,
  created datetime default current_timestamp,
  updated datetime default current_timestamp,
  foreign key(invoiceId) references invoices(id) on delete cascade
);
create table if not exists templates(
  id text primary key,
  userId text not null,
  isDefault integer default 1,
  name text not null,
  markup text,
  created datetime default current_timestamp,
  updated datetime default current_timestamp,
  foreign key(userId) references users(id) on delete cascade
);
create index if not exists idx_invoices_userId on invoices(userId);
create index if not exists idx_clients_userId on clients(userId);
create index if not exists idx_invoice_items_invoiceId on invoice_items(invoiceId);
create index if not exists idx_templates_userId on templates(userId);

drop trigger if exists update_invoice_amountDue_insert;
create trigger update_invoice_amountDue_insert
after insert on invoice_items
for each row
begin
  update invoices set
    amountDue = (select sum(amount) from invoice_items where invoiceId = new.invoiceId),
    updated = current_timestamp
  where id = new.invoiceId;
end;
drop trigger if exists update_invoice_amountDue_update;
create trigger update_invoice_amountDue_update
after update on invoice_items
for each row
begin
  update invoices set
    amountDue = (select sum(amount) from invoice_items where invoiceId = new.invoiceId),
    updated = current_timestamp
  where id = new.invoiceId;
end;
drop trigger if exists update_invoice_amountDue_delete;
create trigger update_invoice_amountDue_delete
after delete on invoice_items
for each row
begin
  update invoices set
    amountDue = (select sum(amount) from invoice_items where invoiceId = old.invoiceId),
    updated = current_timestamp
  where id = old.invoiceId;
end;