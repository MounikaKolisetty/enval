import { Component, OnInit} from '@angular/core';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatIconModule } from '@angular/material/icon';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { AuthService } from '../services/auth.service';
import { UserService } from '../services/user.service';
import { tap } from 'rxjs/operators';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [
    MatToolbarModule, 
    MatIconModule, 
    RouterOutlet, 
    RouterLink, 
    RouterLinkActive, 
    CommonModule,
    HttpClientModule
  ],
  providers: [UserService], // Add UserService as a provider
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent{
  userFullName = '';
  userInitials =  '';
  isLoggedIn = false;
  constructor(private authService: AuthService, private router: Router, private userService: UserService){}
  ngOnInit(){
    this.authService.currentNameSource.subscribe(userName => {
      this.userFullName = userName;
      this.userInitials = this.getInitials(userName);
    });
    this.authService.currentisLoggedIn.subscribe(loggedIn => this.isLoggedIn = loggedIn);
    if (this.LoggedIn()) {
      this.isLoggedIn = true; 
      this.userFullName = localStorage.getItem('UserName') || ''; 
      this.userInitials = this.getInitials(this.userFullName);
    }
  }
  getInitials(fullName: string): string {
    const nameParts = fullName.split(' '); 
    if (nameParts.length >= 2) 
    { 
        return nameParts[0].charAt(0).toUpperCase() + nameParts[1].charAt(0).toUpperCase(); 
    } 
    else if (nameParts.length === 1) 
    {
      return nameParts[0].charAt(0).toUpperCase() + nameParts[0].charAt(1).toUpperCase(); 
    } 
    else 
    { 
      return ''; 
    } 
  }
  logout(){
    this.userService.logout().pipe(
      tap(response => {
        console.log(response.message); 
        this.isLoggedIn = false;
      }) 
    ).subscribe( 
      () => { 
        this.router.navigate(['/login']); 
      } 
    );
  }
  LoggedIn(): boolean {
    return !!localStorage.getItem('UserName'); 
  }
}
