export default class Gate {

	constructor(user) {
		this.user = user;
	}

	superAdmin() {
		return this.user.role === 'super_admin';
	}

	hotelReceptionist() {
		return this.user.role === 'hotel_receptionist';
	}

	hotelOwner() {
		return this.user.role === 'hotel_owner';
	}

	superAdminOrhotelOwner() {
		if(this.user.role === 'hotel_owner' || this.user.role === 'super_admin')
			return	true;
	}

	superAdminOrhotelOwnerOrhotelReceptionist() {
		if(this.user.role === 'hotel_owner' || this.user.role === 'super_admin' || this.user.role === 'hotel_receptionist')
			return	true;
	}

}