import { Component, OnInit} from '@angular/core';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatIconModule } from '@angular/material/icon';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { AuthService } from '../services/auth.service';
import { UserService } from '../services/user.service';

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
  isLoggedIn = false;
  constructor(private authService: AuthService, private router: Router, private userService: UserService){}
  ngOnInit(){
    this.authService.currentNameSource.subscribe(userName => this.userFullName = userName);
    this.authService.currentisLoggedIn.subscribe(loggedIn => this.isLoggedIn = loggedIn);
  }
  logout(){
    this.userService.logout().subscribe(
      response=>{
        console.log(response.message);
      }
    );
    this.authService.changeIsLoggedin(false);
    this.router.navigate(['/login']);
  }
}
