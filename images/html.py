Portraits=["1.JPG","2.jpg","3.jpg","4.jpg","5.jpg","6.JPG","9.jpg","14.JPG"]
i=0
for portrait in Portraits:
    print(f"""                <div class="portfolio-item wild">
                    <img src="images/wildlife/{portrait}" alt="wild Photography">
                    <div class="overlay">
                        <h3>Caption {i}</h3>
                        <p>wildlife</p>
                    </div>
                </div>""")
    i+=1
