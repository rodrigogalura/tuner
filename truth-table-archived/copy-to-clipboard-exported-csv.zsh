#!/bin/zsh

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/truth-table"

select_csv () {
    local files=($(find $DIR -mindepth 1 -maxdepth 1 -name "*.csv" -exec basename {} \; | sort))
    files+=("")

    echo "Select file:"
    for (( i = 1; i < $#files; i++ )); do
        echo "\t[$i]: $files[$i]"
    done
    echo "\n"

    while [[ true ]]; do
        read "?Enter choice: " i

        if [[ $i -ge 1 && $i -lt ${#files[@]} ]]; then
            copy_to_clipboard "$DIR/$files[$i]";

            break;
        else
            echo "Invalid Selection.\n\n"
        fi
    done
}

copy_to_clipboard () {
    # Detect OS
    local OS="$(uname)"

    if [[ "$OS" == "Darwin" ]]; then
        # macOS
        php $DIR/exporter.php $1 | pbcopy && echo "✅ Copied to clipboard!"
    elif [[ "$OS" == "Linux" ]]; then
        # Linux
        php $DIR/exporter.php $1 | xclip -selection clipboard && echo "✅ Copied to clipboard!"
    elif [[ "$OS" == MINGW* || "$OS" == CYGWIN* || "$OS" == MSYS* ]]; then
        # Windows (Git Bash, etc.)
        php $DIR/exporter.php $1 | clip && echo "✅ Copied to clipboard!"
    else
        echo "Unsupported OS: $OS"
    fi
}

select_csv
