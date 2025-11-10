-- Ajoute un champ avatar optionnel pour stocker une URL vers l'avatar
ALTER TABLE utilisateurs
  ADD COLUMN IF NOT EXISTS avatar VARCHAR(1024) NULL AFTER password;