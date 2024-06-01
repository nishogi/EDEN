window.addEventListener('load', function(){
	const tbody	= document.querySelector('#tab tbody');
	const thx	= document.querySelectorAll('#tab thead th');
	const athx	= Array.from(thx);
	const trxb	= tbody.querySelectorAll('tr');
	const atrxb	= Array.from(trxb);
	const compare = function(ids, asc){
		return function(row1, row2){
			const tdValue = function(row, ids){
				return row.children[ids].textContent;
			}
			const tri = function(v1, v2){
				if (v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2)){
					return v1 - v2;
				}	
				else {
					return v1.toString().localeCompare(v2);
				}
				return v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.localeCompare(v2);
			};
			return tri(tdValue(asc ? row1 : row2, ids), tdValue(asc ? row2 : row1, ids));
		}
	}
	thx.forEach(function(th) { 
		th.classList.add('sort');
		th.classList.add('desc');
		th.addEventListener('click', function() {
			//let classe = atrxb.sort(compare(athx.indexOf(th), this.asc = !this.asc));
			let classe = atrxb.sort(compare(athx.indexOf(th), this.asc = !this.asc));
			if(this.asc){
				 this.classList.add('asc');
				 this.classList.remove('desc');
			}
			else{
				this.classList.add('desc');
				this.classList.remove('asc');
			}
			classe.forEach(function(tr) {
					tbody.appendChild(tr);
			});
		});
	});
});
