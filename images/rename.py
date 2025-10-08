import os

def rename_images(relative_folder):
    """
    Rename all image files in the given folder (relative to current location)
    with serial numbers like 1.jpg, 2.png, 3.webp etc.
    """
    # Get absolute path of script's current location
    base_path = os.getcwd()
    folder_path = os.path.join(base_path, relative_folder)

    # Allowed image extensions
    image_extensions = (".jpg", ".jpeg", ".png", ".bmp", ".webp","heic")

    # Get all images
    files = [f for f in os.listdir(folder_path) if f.lower().endswith(image_extensions)]

    # Sort alphabetically (or by modification time if needed)
    files.sort()

    # Rename with serial numbers
    for i, filename in enumerate(files, start=1):
        ext = os.path.splitext(filename)[1]  # keep original extension
        new_name = f"{i}{ext}"
        old_path = os.path.join(folder_path, filename)
        new_path = os.path.join(folder_path, new_name)

        os.rename(old_path, new_path)
        print(f"Renamed: {filename} -> {new_name}")

# ------------------ RUN PROGRAM ------------------ #
if __name__ == "__main__":
    folder_name = input("Enter the folder name (relative to this script): ").strip()
    rename_images(folder_name)
