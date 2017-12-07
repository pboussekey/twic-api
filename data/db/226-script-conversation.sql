UPDATE message, message_user
SET message.user_id = message_user.from_id
WHERE message_user.message_id = message.id AND message_user.user_id = message_user.from_id;

UPDATE message,  message_doc
SET message.library_id = message_doc.library_id
WHERE  message_doc.message_id = message.id;