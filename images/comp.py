import os
from PIL import Image

def compress_images(relative_folder, quality=60):
    """
    Compress all images in the given folder (relative path).
    
    Parameters:
        relative_folder (str): Folder name relative to the script's current location.
        quality (int): JPEG quality (1-100), lower = smaller size.
    """
    # Get the absolute path of the script's current location
    base_path = os.getcwd()
    input_folder = os.path.join(base_path, relative_folder)
    output_folder = os.path.join(input_folder, "compressed")
    
    # Create output folder if it doesn't exist
    os.makedirs(output_folder, exist_ok=True)
    
    # Allowed image extensions
    image_extensions = (".jpg", ".jpeg", ".png", ".bmp", ".webp")
    
    for filename in os.listdir(input_folder):
        if filename.lower().endswith(image_extensions):
            img_path = os.path.join(input_folder, filename)
            save_path = os.path.join(output_folder, filename)
            
            try:
                img = Image.open(img_path)
                
                # Convert PNG/WebP/BMP to RGB before saving as JPEG
                if filename.lower().endswith((".png", ".webp", ".bmp")):
                    img = img.convert("RGB")
                    save_path = os.path.splitext(save_path)[0] + ".jpg"
                
                # Save compressed image
                img.save(save_path, "JPEG", optimize=True, quality=quality)
                print(f"Compressed: {filename} -> {os.path.basename(save_path)}")
            
            except Exception as e:
                print(f"Error compressing {filename}: {e}")

# ------------------ RUN PROGRAM ------------------ #
if __name__ == "__main__":
    folder_name = input("Enter the folder name (relative to this script): ").strip()
    compress_images(folder_name, quality=60)
