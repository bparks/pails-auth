-- Extend permissions to support either user_id or group_id (exactly one per row).
-- Application code must ensure only one of user_id, group_id is set per record.

alter table permissions modify user_id integer null;
alter table permissions add column group_id integer null;
