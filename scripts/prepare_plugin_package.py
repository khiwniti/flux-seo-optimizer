import os
import shutil

# Define the root directory of the project
# Assumes this script is in a 'scripts' subdirectory of the project root
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
PLUGIN_NAME = 'flux-seo-enhanced-gemini' # Main plugin slug, used for some file names
PREPARATION_DIR_NAME = 'flux-seo-optimizer' # The temporary build directory
PREPARATION_PATH = os.path.join(PROJECT_ROOT, PREPARATION_DIR_NAME)

# List of files and directories to include in the plugin package
# Paths are relative to the PROJECT_ROOT
FILES_TO_INCLUDE = [
    f'{PLUGIN_NAME}.php',
    'flux-seo-keyword-scoring-engine.php',
    'flux-seo-auto-blog-scheduler.php',
    'flux-seo-enhanced-gemini.js',
    'flux-seo-auto-blog.js',
    'react.production.min.js',
    'react-dom.production.min.js',
    'flux-seo-enhanced-gemini.css',
    'README.md',
    # Add other root files here if necessary
]

# Directories to copy entirely
# The key is the source directory (relative to PROJECT_ROOT)
# The value is the target directory name within the PREPARATION_PATH
DIRS_TO_COPY = {
    'tabs': 'tabs',
    'languages': 'languages', # Assuming you have a languages folder
    # 'assets': 'assets', # Example: if you have an assets folder
}

SILENCE_IS_GOLDEN = "<?php\n// Silence is golden.\n?>"

def create_index_file(path):
    """Creates a PHP index file to prevent directory listing."""
    index_file_path = os.path.join(path, 'index.php')
    with open(index_file_path, 'w') as f:
        f.write(SILENCE_IS_GOLDEN)
    print(f"Created: {index_file_path}")

def main():
    print(f"Starting plugin preparation for '{PREPARATION_DIR_NAME}'...")
    print(f"Project root: {PROJECT_ROOT}")
    print(f"Preparation path: {PREPARATION_PATH}")

    # Remove the preparation directory if it already exists, then recreate it
    if os.path.exists(PREPARATION_PATH):
        print(f"Removing existing preparation directory: {PREPARATION_PATH}")
        shutil.rmtree(PREPARATION_PATH)
    os.makedirs(PREPARATION_PATH)
    print(f"Created preparation directory: {PREPARATION_PATH}")

    # Create base index.php in the preparation directory root
    create_index_file(PREPARATION_PATH)

    # Copy specified root files
    for file_name in FILES_TO_INCLUDE:
        source_path = os.path.join(PROJECT_ROOT, file_name)
        dest_path = os.path.join(PREPARATION_PATH, os.path.basename(file_name)) # Use os.path.basename for safety
        if os.path.exists(source_path):
            shutil.copy2(source_path, dest_path)
            print(f"Copied: {file_name} to {dest_path}")
        else:
            print(f"Warning: Source file not found, skipping: {source_path}")

    # Copy specified directories
    for src_dir_name, dest_dir_name in DIRS_TO_COPY.items():
        source_dir_path = os.path.join(PROJECT_ROOT, src_dir_name)
        dest_dir_path = os.path.join(PREPARATION_PATH, dest_dir_name)

        if os.path.isdir(source_dir_path):
            shutil.copytree(source_dir_path, dest_dir_path)
            print(f"Copied directory: {src_dir_name} to {dest_dir_path}")
            # Create index.php in the copied directory
            create_index_file(dest_dir_path)
        else:
            print(f"Warning: Source directory not found, skipping: {source_dir_path}")

    print(f"\nPlugin files prepared in: {PREPARATION_PATH}")
    print("Next step would typically be to zip this directory.")

if __name__ == '__main__':
    main()
