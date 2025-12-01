-- Seed Content for Page Content Management
-- This file populates the page_content table with initial/default content

-- Clear existing content (optional - comment out if you want to preserve existing data)
DELETE FROM page_content;

-- About Page Content
INSERT INTO page_content (page, section, content) VALUES
('about', 'who_we_are', 'At Osem Clicks, we are passionate storytellers who capture life''s most precious moments through the lens. Based in the beautiful coastal town of Kundapura, Karnataka, we specialize in creating stunning visual narratives that preserve memories for a lifetime.

Our photography services range from intimate portrait sessions to grand wedding celebrations, from commercial product shoots to breathtaking landscape photography. We believe that every photograph tells a unique story, and we''re committed to telling yours with creativity, professionalism, and heart.

With years of experience and a keen eye for detail, we transform ordinary moments into extraordinary memories. Our approach combines technical expertise with artistic vision, ensuring that every shot captures not just an image, but an emotion, a feeling, a moment in time that you''ll treasure forever.'),

('about', 'our_story', 'The journey of Osem Clicks began with a simple passion for photography and a desire to capture the world through a different lens. What started as a hobby quickly evolved into a full-fledged profession driven by the love for visual storytelling.

Founded by Keerthan B, a talented photographer from Kundapura, Osem Clicks has grown from capturing local events to serving clients across Karnataka and beyond. Every project we undertake is approached with fresh eyes and creative energy, ensuring that our work remains innovative and inspiring.

Our name "Osem" (awesome) reflects our commitment to excellence and our promise to deliver photography that truly amazes. Over the years, we''ve had the privilege of documenting countless weddings, events, and special moments, each one adding to our rich tapestry of experiences.

Today, Osem Clicks stands as a trusted name in photography, known for our professionalism, creativity, and dedication to client satisfaction. We continue to push boundaries, embrace new techniques, and evolve with the ever-changing world of photography while staying true to our core values of authenticity and artistic integrity.');

-- Services Page Content
INSERT INTO page_content (page, section, content) VALUES
('services', 'event_shoots', 'Capture every special moment of your events with our professional event photography services. From corporate conferences and product launches to birthday celebrations and cultural festivals, we document your events with precision and creativity. Our unobtrusive approach ensures natural moments are captured while maintaining comprehensive coverage of all key highlights.'),

('services', 'product_shoots', 'Showcase your products in the best light with our commercial product photography. We create stunning visuals for e-commerce, catalogs, advertising, and marketing materials. Our expertise includes creative styling, precise lighting, and post-production that makes your products stand out and drive sales.'),

('services', 'content_creation', 'Elevate your brand''s online presence with our content creation services. We produce high-quality images and videos tailored for social media platforms, websites, and digital marketing campaigns. From Instagram reels to YouTube thumbnails, we help you create engaging visual content that resonates with your audience.'),

('services', 'brand_corporate', 'Build a strong visual identity for your business with our brand and corporate photography services. We create professional headshots, team photos, office environment shots, and brand storytelling imagery that reflects your company''s values and culture. Perfect for websites, annual reports, and corporate communications.'),

('services', 'editing_post', 'Transform good photos into great ones with our professional editing and post-production services. We offer color correction, retouching, background removal, HDR processing, and creative compositing. Whether you need basic enhancements or complex photo manipulation, we deliver polished results that exceed expectations.'),

('services', 'cinematic', 'Experience the magic of cinematic photography and videography. Our specialized services include drone photography, time-lapse videos, slow-motion captures, and cinematic wedding films. We use cutting-edge equipment and techniques to create visually stunning content that tells your story in the most captivating way possible.');

-- You can add more sections as needed
-- Example for home page if you want to make it dynamic:
-- INSERT INTO page_content (page, section, content) VALUES
-- ('home', 'hero_tagline', 'Capturing Moments, Creating Memories'),
-- ('home', 'hero_description', 'Professional photography services for every occasion');
