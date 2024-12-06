create table
  if not exists invoices (
    id integer primary key,
    summary text,
    clientId integer,
    details text,
    amountDue number,
    dueDate text,
    amountPaid number,
    paidDate text,
    created text default current_timestamp,
    updated text default current_timestamp
  );

create table
  if not exists clients (
    id integer primary key,
    `name` text,
    email text,
    phone text,
    `address` text,
    company text,
    created text default current_timestamp,
    updated text default current_timestamp
  );

create table
  if not exists items (
    id integer primary key,
    invoiceId integer,
    summary text,
    `type` text,
    amount number,
    purchaseDate text,
    created text default current_timestamp,
    updated text default current_timestamp
  );

create table
  if not exists template (
    id integer primary key,
    markup text default '<h1>[invoice.summary]</h1>',
    created text default current_timestamp,
    updated text default current_timestamp
  );

insert into template default values;