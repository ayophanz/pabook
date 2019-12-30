export default class Gate {

	constructor(user) {
		this.user = user;
	}

	superAdmin() {
		try {
			return this.user.role === 'super_admin';
		}catch(e){return false;}
	}

	hotelReceptionist() {
		try {
			return this.user.role === 'hotel_receptionist';
		}catch(e){return false;}
	}

	hotelOwner() {
		try {
			return this.user.role === 'hotel_owner';
		}catch(e){return false;}
	}

	superAdminOrhotelOwner() {
		try {
			if(this.user.role === 'hotel_owner' || this.user.role === 'super_admin')
				return	true;
		}catch(e){return false;}
	}

	superAdminOrhotelOwnerOrhotelReceptionist() {
		try {
			if(this.user.role === 'hotel_owner' || this.user.role === 'super_admin' || this.user.role === 'hotel_receptionist')
				return	true;
		}catch(e){return false;}
	}
	
}