(function($){

	var at_courts = "--|Handelsgericht Wien|LG Wiener Neustadt|Landesgericht St. Pölten|Landesgericht Krems an der Donau|Landesgericht Korneuburg|Landesgericht Linz|Landesgericht Ried im Innkreis|Landesgericht Steyr|Landesgericht Wels|Landesgericht Salzburg|Landesgericht Eisenstadt|Landesgericht für Zivilrechtssachen Graz|Landesgericht Leoben|Landesgericht Klagenfurt|Landesgericht Innsbruck|Landesgericht Feldkirch";
	
	var de_courts = "--|Aachen|Altenburg|Amberg|Ansbach|Apolda|Arnsberg|Arnstadt|Arnstadt Zweigstelle Ilmenau|Aschaffenburg|Augsburg|Aurich|Bad Hersfeld|Bad Homburg v.d.H.|Bad Kreuznach|Bad Oeynhausen|Bad Salzungen|Bamberg|Bayreuth|Berlin (Charlottenburg)|Bielefeld|Bochum|Bonn|Braunschweig|Bremen|Chemnitz|Coburg|Coesfeld|Cottbus|Darmstadt|Deggendorf|Dortmund|Dresden|Duisburg|Düren|Düsseldorf|Eisenach|Erfurt|Eschwege|Essen|Flensburg|Frankfurt am Main|Frankfurt/Oder|Freiburg|Friedberg|Fritzlar|Fulda|Fürth|Gelsenkirchen|Gera|Gießen|Gotha|Göttingen|Greiz|Gütersloh|Hagen|Hamburg|Hamm|Hanau|Hannover|Heilbad Heiligenstadt|Hildburghausen|Hildesheim|Hof|Homburg|Ingolstadt|Iserlohn|Jena|Kaiserslautern|Kassel|Kempten (Allgäu)|Kiel|Kleve|Koblenz|Köln|Königstein|Korbach|Krefeld|Landau|Landshut|Langenfeld|Lebach|Leipzig|Lemgo|Limburg|Lübeck|Ludwigshafen a.Rhein (Ludwigshafen)|Lüneburg|Mainz|Mannheim|Marburg|Meiningen|Memmingen|Merzig|Mönchengladbach|Montabaur|Mühlhausen|München|Münster|Neubrandenburg|Neunkirchen|Neuruppin|Neuss|Nordhausen|Nürnberg|Offenbach am Main|Oldenburg (Oldenburg)|Osnabrück|Ottweiler|Paderborn|Passau|Pinneberg|Pößneck|Pößneck Zweigstelle Bad Lobenstein|Potsdam|Recklinghausen|Regensburg|Rostock|Rudolstadt|Rudolstadt Zweigstelle Saalfeld|Saarbrücken|Saarlouis|Schweinfurt|Schwerin|Siegburg|Siegen|Sömmerda|Sondershausen|Sonneberg|St. Ingbert (St Ingbert)|St. Wendel (St Wendel)|Stadthagen|Stadtroda|Steinfurt|Stendal|Stralsund|Straubing|Stuttgart|Suhl|Tostedt|Traunstein|Ulm|Völklingen|Walsrode|Weiden i. d. OPf.|Weimar|Wetzlar|Wiesbaden|Wittlich|Wuppertal|Würzburg|Zweibrücken";
	
	$.fn.stateselector = function(court_input_selector){
		var $country_input = $(this);
		var $court_input = $(court_input_selector);
		
		var $courts_selected = $('#_organization_registration_authority');
		
		if ($country_input.val()=='AT') {
			var court_arr = at_courts.split("|");
			
			for (var i=0; i<court_arr.length; i++) {
				if ($courts_selected.val()==court_arr[i]) {
					$court_input.append('<option value="'+ court_arr[i] +'" selected>'+ court_arr[i] +'</option>');
				} else {
					$court_input.append('<option value="'+ court_arr[i] +'">'+ court_arr[i] +'</option>');
				}
				
			}
		}
		
		if ($country_input.val()=='DE') {
			var court_arr = de_courts.split("|");
			
			for (var i=0; i<court_arr.length; i++) {
				if ($courts_selected.val()==court_arr[i]) {
					$court_input.append('<option value="'+ court_arr[i] +'" selected>'+ court_arr[i] +'</option>');
				} else {
					$court_input.append('<option value="'+ court_arr[i] +'">'+ court_arr[i] +'</option>');
				}
				
			}
		}
		
		$country_input.change(function(){
			$court_input.children().remove();
			switch($country_input.val()){

				case "AT":

                    $("div#other_country_info").hide();
                    $("div#regform_left").show();
                    $("div#regform_right").show();
                    $("div#regform_buttons").show();

                    var court_arr = at_courts.split("|");
					
					for (var i=0; i<court_arr.length; i++) {
						$court_input.append('<option value="'+ court_arr[i] +'">'+ court_arr[i] +'</option>');
					}
					
				break;

				case "DE":

                    $("div#other_country_info").hide();
                    $("div#regform_left").show();
                    $("div#regform_right").show();
                    $("div#regform_buttons").show();
					var court_arr = de_courts.split("|");
					
					for (var i=0; i<court_arr.length; i++) {
						$court_input.append('<option value="'+ court_arr[i] +'">'+ court_arr[i] +'</option>');
					}
				break;

				case "OTHER":

                    $("div#other_country_info").show();
                    $("div#regform_left").hide();
                    $("div#regform_right").hide();
                    $("div#regform_buttons").hide();

				break;

				default:
					console.error('No valid country selected');
				break;
			}
		});
	};
})(jQuery);