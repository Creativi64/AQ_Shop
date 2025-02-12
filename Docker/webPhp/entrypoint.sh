# #!/bin/bash
# set -e

# cp -R /source/* /app/

# # Change to the application directory.
# cd /app

# # Run the find command to create a php.ini symlink in every directory under /app.
# # Note: Using "$(pwd)" ensures the full path is used.
# find . -type d -exec ln -sf "$(pwd)/php.ini" {}/php.ini \;

# # Execute the main container command (passed as arguments)
# exec "$@"