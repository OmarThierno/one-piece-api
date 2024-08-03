# API One Piece

### requests implemented

#### Get all characters

**URL:** http://localhost:8888/boolean/one-piece-api/server.php
**METHOD:** GET
**RESPONSE:**

- Results => Array
- Number characters => Number
- Success => Boolean

#### Post new character

**URL:** http://localhost:8888/boolean/one-piece-api/server.php
**METHOD:** POST
**REQ BODY:** ["new_character" ,"name", "fullName", "Alias", "epithet", "ability", "dream", "role", "bestTechniques[ ]", "wantedPoster", "photo"]
**RESPONSE:**

- Results => Array
- Number characters => Number
- Success => Boolean


# One Piece Chat

### requests implemented

#### Get message

**URL:** http://localhost:8888/boolean/one-piece-api/chat.php
**METHOD:** POST
**REQ BODY:** ["message" ,"thread_id"]
**RESPONSE:**

- Results => Array ['thread_id', 'message']
- Success => Boolean

